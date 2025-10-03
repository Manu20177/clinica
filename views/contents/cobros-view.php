<?php if($_SESSION['userType']=="Secretaria"): ?>
<?php 
  require_once "./controllers/pagosController.php";
  require_once "./controllers/pacienteController.php";
  $pagosCtrl = new pagosController();
  $pc        = new pacienteController();



  // Sucursal sesión
  $sucId = $_SESSION['userIdSuc'];
  $sucStmt = $pc->execute_single_query("SELECT id_suc, nombre FROM sucursales WHERE id_suc='$sucId' LIMIT 1");
  $sucursalActual = $sucStmt && $sucStmt->rowCount()>0 ? $sucStmt->fetch(PDO::FETCH_ASSOC) : null;

  $query2=$pc->execute_single_query("SELECT id_factura  FROM pagos");
  $correlative=($query2->rowCount())+1;
  $id_factura=$pc->generate_code("FAC",5,$correlative);
?>

<div class="container-fluid">
  <div class="page-header">
    <h1 class="text-titles"><i class="zmdi zmdi-money zmdi-hc-fw"></i> Cobros</h1>
  </div>
  <p class="lead">Registre cobros y genere su comprobante. Los campos con * son obligatorios.</p>
</div>

<div class="container-fluid">
  <ul class="breadcrumb breadcrumb-tabs">
    <li class="active">
      <a href="<?php echo SERVERURL; ?>cobros/" class="btn btn-info">
        <i class="zmdi zmdi-plus"></i> Nuevo
      </a>
    </li>
    <li>
      <a href="<?php echo SERVERURL; ?>cobroslist/" class="btn btn-success">
        <i class="zmdi zmdi-format-list-bulleted"></i> Lista
      </a>
    </li>
  </ul>
</div>

<style>
  .totales .form-control[readonly]{ background:#f9f9f9; font-weight:bold; }
  .table-items th, .table-items td{ vertical-align: middle !important; }
  .w-100{ width:100%; }
  .min-90{ min-width: 90px; }
</style>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="zmdi zmdi-receipt"></i> Nuevo Cobro</h3>
        </div>
        <div class="panel-body">
          <form id="formCobro" action="" method="POST" autocomplete="off">
            <fieldset>
              <legend><i class="zmdi zmdi-file-text"></i> Datos del Comprobante </legend><br>

              <div class="row">
                <!-- ID factura (auto) -->
                <!-- ID factura (auto) -->
				<div class="col-xs-12 col-sm-4">
				<div class="form-group label-floating is-focused">
					<label class="control-label">N.º Comprobante</label>
					<input type="text" id="factura_id_view" class="form-control" value="<?php echo htmlspecialchars($id_factura,ENT_QUOTES,'UTF-8'); ?>" readonly>
					<input type="hidden" id="factura_id" name="factura_id" value="<?php echo htmlspecialchars($id_factura,ENT_QUOTES,'UTF-8'); ?>">
				</div>
				</div>


                <!-- Fecha de pago (solo vista) -->
                <div class="col-xs-12 col-sm-4">
                  <div class="form-group label-floating is-focused">
                    <label class="control-label">Fecha</label>
                    <input type="text" id="fecha_pago_view" class="form-control" value="<?php echo date('Y-m-d H:i'); ?>" readonly>
                  </div>
                </div>

                <!-- Sucursal (de sesión) -->
                <div class="col-xs-12 col-sm-4">
                  <div class="form-group label-floating is-focused">
                    <label class="control-label">Sucursal *</label>
                    <input type="text" class="form-control" value="<?php echo $sucursalActual ? htmlspecialchars($sucursalActual['nombre'],ENT_QUOTES,'UTF-8') : '—'; ?>" readonly>
                    <input type="hidden" name="sucursal_id" value="<?php echo htmlspecialchars($sucId,ENT_QUOTES,'UTF-8'); ?>">
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- Paciente -->
                <div class="col-xs-12 col-sm-6">
                  <div class="form-group label-floating is-focused">
                    <label class="control-label">Paciente (cédula / nombres) *</label>
                    <select id="paciente_id" name="paciente_id" class="form-control" style="width:100%;" required></select>
                  </div>
                </div>

                <!-- Cita (opcional) -->
                <div class="col-xs-12 col-sm-6">
                  <div class="form-group label-floating">
                    <label class="control-label">Cita (opcional)</label>
                    <select id="id_cita" name="id_cita" class="form-control" style="width:100%;"></select>
                  </div>
                </div>

              </div>

              <div class="row">
                <!-- Método -->
                <div class="col-xs-12 col-sm-4">
                  <div class="form-group label-floating is-focused">
                    <label class="control-label">Método de pago *</label>
                    <select class="form-control" name="metodo" id="metodo" required>
                      <option value="EFECTIVO">Efectivo</option>
                      <option value="TARJETA">Tarjeta</option>
                      <option value="TRANSFERENCIA">Transferencia</option>
                      <option value="CHEQUE">Cheque</option>
                      <option value="OTRO">Otro</option>
                    </select>
                  </div>
                </div>

                <!-- Referencia -->
                <div class="col-xs-12 col-sm-4">
                  <div class="form-group label-floating">
                    <label class="control-label">Referencia (voucher, N.º transferencia, etc.)</label>
                    <input type="text" class="form-control" name="referencia" maxlength="80">
                  </div>
                </div>

                <!-- Moneda -->
                <div class="col-xs-12 col-sm-4">
                  <div class="form-group label-floating is-focused">
                    <label class="control-label">Moneda</label>
                    <input type="text" class="form-control" name="moneda" value="USD" readonly>
                  </div>
                </div>
              </div>
            </fieldset>

            <fieldset>
              <legend><i class="zmdi zmdi-collection-text"></i> Ítems</legend><br>

              <div class="table-responsive">
                <table class="table table-striped table-bordered table-items" id="tablaItems">
                  <thead>
                    <tr>
                      <th style="width:45%;">Descripción *</th>
                      <th class="text-center min-90" style="width:10%;">Cant.</th>
                      <th class="text-center min-90" style="width:15%;">P. Unit</th>
                      <th class="text-center min-90" style="width:15%;">Importe</th>
                      <th style="width:10%;"></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>

              <p>
                <button type="button" id="btnAddItem" class="btn btn-default btn-sm">
                  <i class="zmdi zmdi-plus"></i> Agregar ítem
                </button>
              </p>

              <div class="row totales">
                <div class="col-sm-4 col-sm-offset-8">
                  <div class="form-group">
                    <label>Subtotal</label>
                    <input type="text" id="subtotal" name="subtotal" class="form-control text-right" value="0.00" readonly>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">IVA %</span>
                      <input type="number" id="iva_pct" class="form-control text-right" value="12" min="0" max="100" step="0.01">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Impuesto</label>
                    <input type="text" id="impuesto" name="impuesto" class="form-control text-right" value="0.00" readonly>
                  </div>
                  <div class="form-group">
                    <label>Total</label>
                    <input type="text" id="total" name="total" class="form-control text-right" value="0.00" readonly>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label">Observaciones</label>
                <textarea class="form-control" name="notas" rows="2" placeholder="Notas u observaciones (opcional)"></textarea>
              </div>

              <!-- Campos ocultos -->
              <input type="hidden" name="items_json" id="items_json" value="">
            </fieldset>

            <p class="text-center" style="margin-top:15px;">
              <button id="btnGuardarCobro" type="submit" class="btn btn-info btn-raised btn-sm">
                <i class="zmdi zmdi-floppy"></i> Guardar cobro
              </button>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const SERVER = '<?php echo SERVERURL; ?>';

// ---------- Helpers ----------
function toNum(v){
  v = (v ?? '').toString().trim();
  if(!v) return 0;

  const hasComma = v.indexOf(',') !== -1;
  const hasDot   = v.indexOf('.') !== -1;

  if (hasComma && hasDot) {
    // Caso “1.234,56” → quitar separadores de miles (.) y usar coma como decimal
    v = v.replace(/\./g, '').replace(',', '.');
  } else if (hasComma && !hasDot) {
    // Caso “1234,56” → usar coma como decimal
    v = v.replace(',', '.');
  } else {
    // Caso “1234.56” → ya está OK (no borrar puntos)
  }

  const n = parseFloat(v);
  return Number.isFinite(n) ? n : 0;
}

function nf(v){ return (parseFloat(v||0)).toFixed(2); }

function rowTemplate(){
  return `
    <tr>
      <td><input type="text" class="form-control desc" maxlength="160" placeholder="Descripción del servicio o producto" required></td>
      <td><input type="number" inputmode="decimal" class="form-control text-right cant" min="0.01" step="0.01" value="1"></td>
      <td><input type="number" inputmode="decimal" class="form-control text-right punit" min="0" step="0.01" value="0.00"></td>
      <td><input type="text" class="form-control text-right importe" value="0.00" readonly></td>
      <td class="text-center">
        <button type="button" class="btn btn-danger btn-xs btnDelItem"><i class="zmdi zmdi-delete"></i></button>
      </td>
    </tr>
  `;
}

function recalc(){
  let subtotal = 0;
  $('#tablaItems tbody tr').each(function(){
    const cant  = toNum($(this).find('.cant').val());
    const punit = toNum($(this).find('.punit').val());
    const imp   = Math.max(0, cant) * Math.max(0, punit);
    $(this).find('.importe').val(nf(imp));
    subtotal += imp;
  });
  $('#subtotal').val(nf(subtotal));
  const ivaPct = Math.max(0, toNum($('#iva_pct').val()));
  const impuesto = subtotal * (ivaPct/100);
  $('#impuesto').val(nf(impuesto));
  $('#total').val(nf(subtotal + impuesto));
}

function buildItemsJson(){
  const items = [];
  let valido = true;
  $('#tablaItems tbody tr').each(function(){
    const $row  = $(this);
    const desc  = ($row.find('.desc').val() || '').trim();
    const cant  = toNum($row.find('.cant').val());
    const punit = toNum($row.find('.punit').val());
    const imp   = toNum($row.find('.importe').val());

    if(!desc){ valido = false; $row.find('.desc').focus(); return false; }
    if(cant <= 0){ valido = false; $row.find('.cant').focus(); return false; }

    items.push({
      descripcion: desc,
      cantidad: parseFloat(nf(cant)),
      precio_unit: parseFloat(nf(punit)),
      importe: parseFloat(nf(imp))
    });
  });
  if(!valido) return null;
  return JSON.stringify(items);
}

// ---------- Ready ----------
$(function(){
  // Nº comprobante (con reintento simple si falla)
  

  // Select2 paciente
  $('#paciente_id').select2({
    placeholder: 'Escriba cédula o nombre...',
    minimumInputLength: 2,
    width:'100%',
    ajax: {
      url: SERVER+'ajax/ajaxPagos.php',
      type: 'POST',
      dataType: 'json',
      delay: 250,
      data: params => ({ action:'buscar_pacientes_json', q: params.term }),
      processResults: data => ({ results: Array.isArray(data) ? data : [] })
    },
    language: {
      inputTooShort: () => "Escriba al menos 2 caracteres...",
      noResults:     () => "Sin resultados",
      searching:     () => "Buscando..."
    }
  });
  // Cuando se seleccione un paciente, inicializamos el select de citas
  $('#paciente_id').on('change', function(){
    const pacId = $(this).val();
    if(!pacId){
      $('#id_cita').empty().trigger('change');
      return;
    }
    $('#id_cita').select2({
      placeholder: 'Seleccione una cita (Opcional)...',
      allowClear: true,
      width:'100%',
      ajax: {
        url: SERVER+'ajax/ajaxPagos.php',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({ action:'citas_por_paciente', paciente_id: pacId, q: params.term }),
        processResults: data => ({ results: Array.isArray(data) ? data : [] })
      },
      language: {
        noResults: () => "Sin citas disponibles",
        searching: () => "Buscando..."
      }
    });
  });


  // Ítems
  $('#btnAddItem').on('click', function(){
    $('#tablaItems tbody').append(rowTemplate());
  }).trigger('click'); // agrega 1 fila por defecto

  $('#tablaItems').on('input', '.cant, .punit', recalc);
  $('#iva_pct').on('input', recalc);
  $('#tablaItems').on('click', '.btnDelItem', function(){
    $(this).closest('tr').remove();
    recalc();
  });
  // Normaliza a 2 decimales al salir del campo
	$('#tablaItems').on('blur', '.cant, .punit', function(){
	const n = Math.max(0, toNum($(this).val()));
	$(this).val(nf(n));
	recalc();
	});

  // Submit
  $('#formCobro').on('submit', function(e){
    e.preventDefault();

    const btn = $('#btnGuardarCobro').prop('disabled', true).text('Guardando...');
    const stop = (msg) => { alert(msg); btn.prop('disabled', false).text('Guardar cobro'); };

    // Validaciones mínimas
    const paciente = $('#paciente_id').val();
    if(!paciente) return stop('Seleccione un paciente.');
    if($('#tablaItems tbody tr').length===0) return stop('Agregue al menos un ítem.');

    recalc();
    const total = toNum($('#total').val());
    if(total <= 0) return stop('El total debe ser mayor a 0.');

    const itemsJson = buildItemsJson();
    if(!itemsJson) return stop('Complete correctamente los ítems.');

    // Arma el payload exactamente con los names del form
    const payload = {
      action:       'crear_pago',
      factura_id:   $('#factura_id').val(),
      paciente_id:  $('#paciente_id').val(),
      sucursal_id:  $('input[name="sucursal_id"]').val(),
      id_cita:      $('#id_cita').val() || '',
      metodo:       $('#metodo').val(),
      referencia:   $('input[name="referencia"]').val(),
      moneda:       $('input[name="moneda"]').val(),
      subtotal:     $('#subtotal').val(),
      impuesto:     $('#impuesto').val(),
      total:        $('#total').val(),
      notas:        $('textarea[name="notas"]').val(),
      items_json:   itemsJson
    };

    $.post(SERVER+'ajax/ajaxPagos.php', payload, null, 'json')
      .done(function(r){
        if(r && r.ok){
          alert('Cobro registrado. N.º: ' + (r.id || payload.factura_id));
          // Limpieza y/o redirección
          window.location = SERVER+'cobroslist/';
        }else{
          stop((r && r.error) ? r.error : 'No se pudo guardar');
          // Para depurar:
          if (r && r.debug) console.log('DEBUG:', r.debug);
        }
      })
      .fail(function(xhr){
        stop('Error de comunicación ('+xhr.status+')');
        console.error('crear_pago FAIL', xhr.responseText);
      });
  });


});
</script>

<?php 
  else:
    $logout2 = new loginController();
    echo $logout2->login_session_force_destroy_controller(); 
  endif;
?>
