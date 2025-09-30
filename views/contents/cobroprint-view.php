<?php
// views/cobroprint.php
if (!isset($_SESSION['userType'])) { die('No autorizado'); }

require_once "./controllers/pacienteController.php"; // por connect()
$pc = new pacienteController();

// Ruta: cobroprint/ID_FACTURA
$parts = explode("/", $_GET['views'] ?? '');
$facturaId = isset($parts[1]) ? trim($parts[1]) : '';
if ($facturaId === '') { die('<h3>Falta el parámetro id de factura</h3>'); }

// ===== Cabecera del pago =====
// Ajusta nombres de columnas de pagos según tu esquema real:
$sqlPago = "
  SELECT p.id_factura, p.paciente_id, p.sucursal_id, p.metodo, p.referencia, p.moneda, p.subtotal, p.impuesto, p.total, p.notas, p.creado_por, p.fecha_pago, p.estado, pa.id_paciente, pa.id_paciente AS pac_cod, pa.cedula, pa.nombres, pa.apellidos, pa.telefono, pa.correo, s.id_suc, s.nombre AS sucursal_nombre, s.direccion AS sucursal_direccion, s.telefono AS sucursal_telefono FROM pagos p LEFT JOIN pacientes pa ON pa.id_paciente = p.paciente_id OR pa.id_paciente = p.paciente_id LEFT JOIN sucursales s ON s.id_suc = p.sucursal_id WHERE p.id_factura = :idf
  LIMIT 1
";
$stmt = $pc->connect()->prepare($sqlPago);
$stmt->bindParam(':idf', $facturaId, PDO::PARAM_STR);
$stmt->execute();
$pago = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pago) { die('<h3>No se encontró el comprobante solicitado.</h3>'); }

// ===== Ítems =====
// Ajusta nombres de columnas de pago_items a los tuyos reales
$sqlItems = "
  SELECT descripcion, cantidad, precio_unit, importe FROM pago_items WHERE factura_id =  :idf
  ORDER BY id ASC
";
$stmtIt = $pc->connect()->prepare($sqlItems);
$stmtIt->bindParam(':idf', $facturaId, PDO::PARAM_STR);
$stmtIt->execute();
$items = $stmtIt->fetchAll(PDO::FETCH_ASSOC);

// Helpers
function nf2($n){ return number_format((float)$n, 2, '.', ','); }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$fecha = $pago['fecha_emision'] ?? date('Y-m-d H:i:s');
$pacienteNombre = trim(($pago['nombres'] ?? '').' '.($pago['apellidos'] ?? ''));
$pacienteDoc    = $pago['cedula'] ?: $pago['paciente_id']; // muestra cédula si existe
$sucursalNombre = $pago['sucursal_nombre'] ?: $pago['sucursal_id'];
$sucursalDir    = $pago['sucursal_direccion'] ?? '';
$sucursalTel    = $pago['sucursal_telefono'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Comprobante <?php echo h($pago['id_factura']); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    /* ====== SCOPE: todo dentro de .invoice para no chocar con el layout global ====== */
    .invoice * { box-sizing: border-box; }
    .invoice { --fg:#111; --muted:#555; --border:#ddd; --bg:#fff; --brand:#0d6efd;
               font-family: Arial, Helvetica, sans-serif; color:var(--fg); background:var(--bg); }
    .invoice-wrap{ max-width: 900px; margin:0 auto; background:#fff; padding:20px; }
    .invoice-header{ display:flex; align-items:flex-start; gap:16px; border-bottom:1px solid var(--border); padding-bottom:14px; margin-bottom:16px; }
    .invoice-brand{ font-size:22px; font-weight:700; color:var(--brand); line-height:1.2; }
    .invoice-meta{ margin-left:auto; text-align:right; font-size:14px; color:var(--muted); line-height:1.5; }
    .invoice-badge{ display:inline-block; padding:3px 8px; border-radius:6px; font-size:12px; margin-top:6px; border:1px solid transparent; }
    .invoice-ok{ background:#e8f5e9; color:#1b5e20; border-color:#c8e6c9; }
    .invoice-na{ background:#ffebee; color:#b71c1c; border-color:#ffcdd2; }
    .invoice-grid{ display:grid; grid-template-columns: 1fr 1fr; gap:18px; margin-bottom:14px; }
    .invoice-card{ border:1px solid var(--border); border-radius:8px; padding:12px; }
    .invoice-card h3{ margin:0 0 8px; font-size:14px; text-transform:uppercase; color:#333; letter-spacing:.5px; }
    .invoice-muted{ color:var(--muted); }
    .invoice-table{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .invoice-table th, .invoice-table td{ border:1px solid var(--border); padding:8px; font-size:14px; word-wrap:break-word; }
    .invoice-table th{ background:#f8f9fa; text-align:left; }
    .num{ text-align:right; white-space:nowrap; }
    .invoice-totales{ width: 320px; margin-left: auto; margin-top: 10px; border:1px solid var(--border); border-radius:8px; overflow:hidden; }
    .invoice-totales table{ border:none; table-layout:auto; width:100%; }
    .invoice-totales td{ border:none; padding:8px 10px; }
    .invoice-totales tr + tr td{ border-top:1px solid var(--border); }
    .invoice-lbl{ color:var(--muted); }
    .invoice-grand{ font-weight:700; font-size:16px; }
    .invoice-actions{ display:flex; gap:8px; margin-top:16px; justify-content:flex-end; }
    .invoice-btn{ border:1px solid var(--border); border-radius:8px; padding:8px 14px; background:#fff; cursor:pointer; }
    .invoice-btn.primary{ background:var(--brand); color:#fff; border-color:var(--brand); }
    .invoice-notes{ margin-top:12px; font-size:13px; color:#333; white-space:pre-wrap; }

    @media (max-width: 720px){
      .invoice-wrap{ padding:12px; }
      .invoice-grid{ grid-template-columns: 1fr; }
      .invoice-meta{ text-align:left; margin-left:0; }
    }

    /* ====== PRINT: oculta layout global y evita que se monte ====== */
    @media print{
      body, html{ margin:0; padding:0; }
      nav, aside, header, footer, .navbar, .sidebar, .page-header { display:none !important; }
      .invoice-wrap{ max-width:100%; padding:0; }
      .invoice-actions{ display:none !important; }
    }
  </style>
</head>
<body class="invoice">
  <div class="invoice-wrap">
    <div class="invoice-header">
      <div>
        <div class="invoice-brand"><?php echo h($sucursalNombre ?: 'Clínica'); ?></div>
        <?php if($sucursalDir || $sucursalTel): ?>
          <div class="invoice-muted"><?php echo h($sucursalDir); ?><?php echo $sucursalTel?' · Tel: '.h($sucursalTel):''; ?></div>
        <?php endif; ?>
      </div>
      <div class="invoice-meta">
        <div><strong>Comprobante:</strong> <?php echo h($pago['id_factura']); ?></div>
        <div><strong>Fecha:</strong> <?php echo h(date('Y-m-d H:i', strtotime($fecha))); ?></div>
        <div><strong>Moneda:</strong> <?php echo h($pago['moneda'] ?: 'USD'); ?></div>
        <div>
          <span class="invoice-badge <?php echo ($pago['estado']=='ANULADO'?'invoice-na':'invoice-ok'); ?>">
            <?php echo h($pago['estado'] ?: 'ACTIVO'); ?>
          </span>
        </div>
      </div>
    </div>

    <div class="invoice-grid">
      <div class="invoice-card">
        <h3>Paciente</h3>
        <div><strong><?php echo h($pacienteNombre ?: '—'); ?></strong></div>
        <div class="invoice-muted">Cédula: <?php echo h($pago['cedula'] ?: '—'); ?></div>
        <div class="invoice-muted">ID interno: <?php echo h($pago['id_paciente'] ?: $pago['paciente_id']); ?></div>
        <?php if(!empty($pago['correo'])): ?>
          <div class="invoice-muted">Email: <?php echo h($pago['correo']); ?></div>
        <?php endif; ?>
        <?php if(!empty($pago['telefono'])): ?>
          <div class="invoice-muted">Tel: <?php echo h($pago['telefono']); ?></div>
        <?php endif; ?>
      </div>
      <div class="invoice-card">
        <h3>Pago</h3>
        <div>Método: <strong><?php echo h($pago['metodo']); ?></strong></div>
        <div class="invoice-muted">Referencia: <?php echo h($pago['referencia'] ?: '—'); ?></div>
        <?php if(!empty($pago['sucursal_id'])): ?>
          <div class="invoice-muted">Sucursal: <?php echo h($sucursalNombre ?: $pago['sucursal_id']); ?></div>
        <?php endif; ?>
      </div>
    </div>

    <table class="invoice-table">
      <thead>
        <tr>
          <th style="width:60px">#</th>
          <th>Descripción</th>
          <th class="num" style="width:120px">Cant.</th>
          <th class="num" style="width:120px">P. Unit</th>
          <th class="num" style="width:140px">Importe</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($items && count($items)): ?>
          <?php $i=1; foreach($items as $it): ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo h($it['descripcion']); ?></td>
              <td class="num"><?php echo nf2($it['cantidad']); ?></td>
              <td class="num"><?php echo nf2($it['precio_unit']); ?></td>
              <td class="num"><?php echo nf2($it['importe']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="invoice-muted">Sin ítems</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="invoice-totales">
      <table>
        <tr>
          <td class="invoice-lbl">Subtotal</td>
          <td class="num"><?php echo nf2($pago['subtotal']); ?></td>
        </tr>
        <tr>
          <td class="invoice-lbl">Impuesto</td>
          <td class="num"><?php echo nf2($pago['impuesto']); ?></td>
        </tr>
        <tr>
          <td class="invoice-grand">Total</td>
          <td class="num invoice-grand"><?php echo nf2($pago['total']); ?></td>
        </tr>
      </table>
    </div>

    <?php if(!empty($pago['notas'])): ?>
      <div class="invoice-notes">
        <strong>Observaciones:</strong><br>
        <?php echo nl2br(h($pago['notas'])); ?>
      </div>
    <?php endif; ?>

    <div class="invoice-actions">
      <button class="invoice-btn" onclick="window.history.back()">Volver</button>
      <button class="invoice-btn primary" onclick="window.print()">Imprimir</button>
    </div>
  </div>
</body>
</html>
