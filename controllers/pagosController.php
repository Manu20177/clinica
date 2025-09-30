
<?php
if($actionsRequired){
  require_once "../models/pagosModel.php";
  require_once "../models/agendaModel.php";
}else{
  require_once "./models/pagosModel.php";
  require_once "./models/agendaModel.php";
}

class pagosController extends pagosModel {


  /*****************************************
   *  Buscar pacientes para Select2 (JSON)
   *****************************************/
  public function buscar_pacientes_json_controller($q){
    $q = trim(self::clean_string($q ?? ''));
    if(mb_strlen($q) < 2){
      return json_encode([]);
    }
    // Reutilizamos consultas del pacienteModel
    $rows = self::buscar_pacientes_model($q);
    if(!$rows){ return json_encode([]); }

    $out = [];
    foreach($rows as $r){
      $id  = $r['id_paciente'];
      $ced = $r['cedula'] ?? '';
      $nom = trim(($r['nombres'] ?? '').' '.($r['apellidos'] ?? ''));
      $text = trim(($ced ? $ced.' - ' : '').$nom);
      $out[] = ["id"=>$id, "text"=>$text];
    }
    return json_encode($out, JSON_UNESCAPED_UNICODE);
  }

  /*****************************************
   *  Crear pago + items (transacción)
   *****************************************/
 /* ========== Crear pago (pago + items) → JSON ========== */
  private function num($s){
    // Convierte string a float seguro (ya envías con punto desde JS)
    return (float)str_replace(',', '.', (string)$s);
  }

  public function crear_pago_controller(){
    $debug = isset($_POST['debug']) ? (int)$_POST['debug'] : 0;

    try {
      $factura_id  = trim($_POST['factura_id'] ?? '');
      // ❗ YA NO casteamos a int: son alfanuméricos
      $paciente_id = trim($_POST['paciente_id'] ?? '');
      $sucursal_id = trim($_POST['sucursal_id'] ?? '');
      // id_cita puede ser opcional y numérico; si en tu BD también es string, cámbialo igual que los otros
      $id_cita     = ($_POST['id_cita'] ?? '') === '' ? null : trim($_POST['id_cita']);
      $metodo      = trim($_POST['metodo'] ?? '');
      $referencia  = trim($_POST['referencia'] ?? '');
      $moneda      = trim($_POST['moneda'] ?? 'USD');
      $subtotal    = $this->num($_POST['subtotal'] ?? 0);
      $impuesto    = $this->num($_POST['impuesto'] ?? 0);
      $total       = $this->num($_POST['total'] ?? 0);
      $notas       = trim($_POST['notas'] ?? '');
      $items_json  = $_POST['items_json'] ?? '[]';
      $creado_por  = $_SESSION['userKey'] ?? 'sistema';

      $items = json_decode($items_json, true);
      if(!is_array($items)) $items = [];

      // ✅ Validación para strings alfanuméricos
      $errs = [];
      if($factura_id === '') $errs[] = 'factura_id vacío';
      if($paciente_id === '') $errs[] = 'paciente_id vacío';
      if($sucursal_id === '') $errs[] = 'sucursal_id vacío';
      if($metodo === '') $errs[] = 'metodo vacío';
      if($total <= 0) $errs[] = 'total <= 0';
      if(count($items) === 0) $errs[] = 'items_json vacío o malformado';

      if($errs){
        $out = ["ok"=>false,"error"=>"Datos incompletos o totales inválidos"];
        if($debug){
          $out["debug_detail"] = [
            "factura_id"=>$factura_id,
            "paciente_id"=>$paciente_id,
            "sucursal_id"=>$sucursal_id,
            "metodo"=>$metodo,
            "subtotal"=>$subtotal,
            "impuesto"=>$impuesto,
            "total"=>$total,
            "items_count"=>count($items),
            "errs"=>$errs
          ];
        }
        return json_encode($out);
      }

      // Normalización y recalculo igual que antes...
      $norm = [];
      foreach($items as $i){
        $desc  = trim($i['descripcion'] ?? '');
        $cant  = (float)($i['cantidad'] ?? 0);
        $punit = (float)($i['precio_unit'] ?? 0);
        $imp   = (float)($i['importe'] ?? ($cant*$punit));
        if($desc==='' || $cant<=0){
          return json_encode(["ok"=>false,"error"=>"Ítem inválido"]);
        }
        $norm[] = [
          'factura_id'  => $factura_id,
          'descripcion' => mb_substr($desc,0,160,'UTF-8'),
          'cantidad'    => $cant,
          'precio_unit' => $punit,
          'importe'     => $imp
        ];
      }

      $sub_calc = 0.0;
      foreach($norm as $n) $sub_calc += ($n['cantidad'] * $n['precio_unit']);
      $sub_calc = round($sub_calc,2);
      $tot_calc = round($sub_calc + $impuesto, 2);
      if (abs($subtotal - $sub_calc) > 0.01) $subtotal = $sub_calc;
      if (abs($total - $tot_calc) > 0.01)    $total    = $tot_calc;

      $pago = [
        'id_factura'  => $factura_id,
        'id_cita'     => $id_cita,
        'paciente_id' => $paciente_id, // ← string
        'sucursal_id' => $sucursal_id, // ← string
        'metodo'      => $metodo,
        'referencia'  => $referencia,
        'subtotal'    => $subtotal,
        'impuesto'    => $impuesto,
        'total'       => $total,
        'moneda'      => $moneda ?: 'USD',
        'estado'      => 'EMITIDO',
        'notas'       => $notas,
        'creado_por'  => $creado_por
      ];

      $ok = $this->crear_pago_tx_model($pago, $norm);
      if(!$ok) return json_encode(["ok"=>false,"error"=>"No se pudo registrar el cobro"]);

      return json_encode(["ok"=>true,"id"=>$factura_id]);

    } catch(Exception $e){
      return json_encode(["ok"=>false,"error"=>"Excepción","debug"=>$e->getMessage()]);
    }
  }


  /*****************************************
   *  Anular pago
   *****************************************/
  public function anular_pago_controller($id_factura, $razon){
    $id_factura = self::clean_string($id_factura ?? '');
    $razon      = trim(self::clean_string($razon ?? ''));
    if($id_factura===''){
      return json_encode(['ok'=>false,'error'=>'ID de factura vacío']);
    }
    if($razon===''){
      return json_encode(['ok'=>false,'error'=>'Debe indicar una razón de anulación']);
    }

    $pdo = $this->connect();
    $usuario = $_SESSION['userKey'] ?? 'SYSTEM';

    // Evita anular 2 veces
    $st = $pdo->prepare("SELECT estado FROM pagos WHERE id_factura=:id LIMIT 1");
    $st->execute([':id'=>$id_factura]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if(!$row){
      return json_encode(['ok'=>false,'error'=>'Comprobante no encontrado']);
    }
    if(strtoupper($row['estado'])==='ANULADO'){
      return json_encode(['ok'=>false,'error'=>'El comprobante ya está anulado']);
    }

    $up = $pdo->prepare("UPDATE pagos
                            SET estado='ANULADO',
                                anulado_por=:usr,
                                anulado_en=NOW(),
                                anulado_razon=:rz
                          WHERE id_factura=:id
                          LIMIT 1");
    $ok = $up->execute([
      ':usr' => $usuario,
      ':rz'  => $razon,
      ':id'  => $id_factura
    ]);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>$ok ? true : false], JSON_UNESCAPED_UNICODE);
    exit;
  }

  /*****************************************
   *  (Opcional) Detalle de pago
   *****************************************/
  public function detalle_pago_controller($id_factura){
    $id_factura = self::clean_string($id_factura ?? '');
    if($id_factura===''){ return ['ok'=>false,'error'=>'ID vacío']; }

    $pdo = $this->connect();
    $h = $pdo->prepare("SELECT * FROM pagos WHERE id_factura=:id LIMIT 1");
    $h->execute([':id'=>$id_factura]);
    $cab = $h->fetch(PDO::FETCH_ASSOC);
    if(!$cab) return ['ok'=>false,'error'=>'No encontrado'];

    $d = $pdo->prepare("SELECT descripcion,cantidad,precio_unit,importe
                          FROM pago_items WHERE factura_id=:id ORDER BY id ASC");
    $d->execute([':id'=>$id_factura]);
    $items = $d->fetchAll(PDO::FETCH_ASSOC);

    return ['ok'=>true, 'pago'=>$cab, 'items'=>$items];
  }

  /*****************************************
   *  (Opcional) Lista para tabla
   *****************************************/
  public function listar_pagos_controller($desde=null,$hasta=null,$sucursal_id=null){
    $pdo = $this->connect();
    $where = [];
    $par = [];
    if($desde){ $where[] = "fecha_pago >= :d"; $par[':d']=$desde; }
    if($hasta){ $where[] = "fecha_pago <= :h"; $par[':h']=$hasta; }
    if($sucursal_id){ $where[] = "sucursal_id = :s"; $par[':s']=$sucursal_id; }
    $w = $where ? ('WHERE '.implode(' AND ',$where)) : '';

    $sql = "SELECT p.*, CONCAT(pa.nombres,' ',pa.apellidos) paciente
              FROM pagos p
         LEFT JOIN pacientes pa ON pa.id_paciente=p.paciente_id
              $w
          ORDER BY p.fecha_pago DESC";
    $st = $pdo->prepare($sql);
    $st->execute($par);
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  public function citas_por_paciente_controller($paciente_id, $q=''){
      $q = self::clean_string($q ?? '');

  

      // Si deseas filtrar por sucursal de sesión:
      $sucursal_id = $_SESSION['userIdSuc'] ?? null;

      $rows = self::citas_por_paciente_model($paciente_id, $sucursal_id, $q);
      if(!$rows){ return json_encode([]); }

      // Formato Select2: [{id,text}]
      $out = [];
      foreach($rows as $r){
          $id     = $r['id'];
          $fecha  = !empty($r['fecha_inicio']) ? date('Y-m-d', strtotime($r['fecha_inicio'])) : '';
          $hi     = !empty($r['fecha_inicio']) ? date('H:i',   strtotime($r['fecha_inicio'])) : '';
          $hf     = !empty($r['fecha_fin'])    ? date('H:i',   strtotime($r['fecha_fin']))    : '';
          $estado = strtoupper($r['estado'] ?? '');
          $esp    = $r['especialidad'] ?? '';
          $med    = trim(($r['mnombres'] ?? '').' '.($r['mapellidos'] ?? ''));

          // Texto visible en el dropdown
          $partes = array_filter([$fecha, "$hi-$hf", $estado, $esp, $med]);
          $text   = implode(' · ', $partes);

          $out[] = ["id"=>$id, "text"=>$text];
      }

      return json_encode($out, JSON_UNESCAPED_UNICODE);
  }

  public function listado_pagos_controller(){
    // Consulta: pagos + (paciente, sucursal)
    // Nota: El JOIN intenta por id_paciente (numérico) o por Codigo (alfanumérico).
    $sql = "
      SELECT p.id_factura, p.fecha_pago, p.paciente_id, p.sucursal_id, p.metodo, p.subtotal, p.impuesto, p.total, p.moneda, p.estado, pa.id_paciente AS pa_id, pa.nombres AS pa_nombres, pa.apellidos AS pa_apellidos, s.id_suc AS s_id, s.nombre AS s_nombre FROM pagos p LEFT JOIN pacientes pa ON (pa.id_paciente = p.paciente_id OR pa.id_paciente = p.paciente_id) LEFT JOIN sucursales s ON s.id_suc = p.sucursal_id ORDER BY p.fecha_pago DESC, p.id_factura DESC
    ";

    $rs = self::execute_single_query($sql);
    $rows = $rs ? $rs->fetchAll(PDO::FETCH_ASSOC) : [];

    // Helpers locales
    $h = function($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
    $money = function($v){ return number_format((float)$v, 2, '.', ','); };
    $badge = function($estado){
      $cls = 'label-default';
      if($estado==='EMITIDO') $cls='label-success';
      if($estado==='ANULADO') $cls='label-danger';
      return '<span class="label '.$cls.'">'.$estado.'</span>';
    };

    // Construye tabla
    $html = '
    <table id="tabla-global" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th class="text-center">N.º Factura</th>
          <th class="text-center">Fecha</th>
          <th class="text-center">Paciente</th>
          <th class="text-center">Sucursal</th>
          <th class="text-center">Método</th>
          <th class="text-center">Subtotal</th>
          <th class="text-center">Impuesto</th>
          <th class="text-center">Total</th>
          <th class="text-center">Estado</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
    ';

    $i = 1;
    foreach($rows as $r){
      $id       = $r['id_factura'];
      $fecha    = $r['fecha_pago'] ? date('d/m/Y H:i', strtotime($r['fecha_pago'])) : '';
      $paciente = trim(($r['pa_nombres'] ?? '').' '.($r['pa_apellidos'] ?? ''));
      if($paciente==='') $paciente = (string)$r['paciente_id']; // fallback
      $sucursal = $r['s_nombre'] ?: (string)$r['sucursal_id'];

      $estado   = $r['estado'];
      $acciones = [];

      // Ver/Imprimir (ajusta la ruta a tu impresor/visor real)
      $acciones[] = '<a href="'.SERVERURL.'cobroprint/'.$this->h($id).'/" class="btn btn-primary btn-raised btn-xs" title="Ver/Imprimir">
        <i class="zmdi zmdi-print"></i>
      </a>';

      // Anular solo si está EMITIDO
      if($estado==='EMITIDO'){
        $acciones[] = '<a href="#!" class="btn btn-danger btn-raised btn-xs btn-anular-pago" data-id="'.$this->h($id).'" title="Anular">
          <i class="zmdi zmdi-block"></i>
        </a>';
      }

      $html .= '
        <tr>
          <td class="text-center">'.$i.'</td>
          <td class="text-center">'.$h($id).'</td>
          <td class="text-center">'.$h($fecha).'</td>
          <td>'.$h($paciente).'</td>
          <td>'.$h($sucursal).'</td>
          <td class="text-center">'.$h($r['metodo']).'</td>
          <td class="text-right">'.$h($r['moneda']).' '.$money($r['subtotal']).'</td>
          <td class="text-right">'.$h($r['moneda']).' '.$money($r['impuesto']).'</td>
          <td class="text-right"><strong>'.$h($r['moneda']).' '.$money($r['total']).'</strong></td>
          <td class="text-center">'.$badge($h($estado)).'</td>
          <td class="text-center">'.implode(' ', $acciones).'</td>
        </tr>
      ';
      $i++;
    }

    if($i===1){
      $html .= '<tr><td colspan="11" class="text-center">No hay cobros registrados.</td></tr>';
    }

    $html .= '
      </tbody>
    </table>
    ';

    return $html;
  }

  // Pequeño helper para usar $this->h(...) arriba en acciones
  private function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }


}
