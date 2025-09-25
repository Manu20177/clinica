<?php
if(isset($_SESSION['userType']) && $_SESSION['userType']==="Secretaria"):
  date_default_timezone_set('America/Guayaquil');
  require_once "./controllers/agendaController.php";
  require_once "./controllers/pacienteController.php";
  $agendaCtrl = new agendaController();
  $pc        = new pacienteController();
  $id_sucursal=$_SESSION['userIdSuc'];

  // Guardar cita (POST directo)  // <-- usa id_especialidad_med
  if(
    isset($_POST['paciente_id'], $_POST['sucursal_id'], $_POST['id_especialidad_med'],
          $_POST['fecha'], $_POST['hora_inicio'], $_POST['hora_fin'])
  ){
      echo $agendaCtrl->add_cita_controller();
  }

  // Sucursal desde sesión (preseleccionada y bloqueada)
  $sucId = $_SESSION['userIdSuc'];
  $sucStmt = $pc->execute_single_query("SELECT id_suc, nombre FROM sucursales WHERE id_suc='$sucId' LIMIT 1");
  $sucursalActual = $sucStmt && $sucStmt->rowCount()>0 ? $sucStmt->fetch(PDO::FETCH_ASSOC) : null;

  // Especialidades
  $espStmt = $pc->execute_single_query("SELECT id, nombre FROM especialidades WHERE estado='Activa' ORDER BY nombre ASC");
?>
<style>


/* Evita stacking raros creados por contenedores */
#calendar, .fc, .container-fluid{ position:relative; z-index:1; }

.fc-toolbar-title { font-size: 1.6rem; }
.fc-timegrid-slot { height: 2.2em; }
.legend { margin-top: 10px; display:flex; gap:18px; align-items:center; flex-wrap:wrap; }
.legend .dot { width:14px; height:14px; display:inline-block; border-radius:3px; margin-right:6px; }
.dot-now { background:#5bc0de; }
.dot-reserved { background:#d9534f; }
.dot-confirmed { background:#5cb85c; }
.fc-event.reservado,
.fc-timegrid-event.reservado { background:#d9534f!important; border-color:#d9534f!important; color:#fff!important; }
.fc-event.estado-confirmada  { background:#5cb85c!important; border-color:#5cb85c!important; color:#fff!important; }
.fc-event.estado-atendida    { background:#0275d8!important; border-color:#0275d8!important; color:#fff!important; }
.fc-now-indicator-line { border-top: 2px solid #5bc0de; }
.select2-container--default .select2-selection--single { height: 38px; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
</style>

<div class="container-fluid">
  <div class="page-header">
    <h1 class="text-titles"><i class="zmdi zmdi-calendar zmdi-hc-fw"></i> Agendar Turno</h1>
  </div>
  <p class="lead">Seleccione la fecha y hora para realizar su trámite.</p>
  <hr>
</div>

<div class="container-fluid">
  <form id="formCita" action="" method="POST" autocomplete="off">
    <!-- Paciente -->
    <div class="row">
      <div class="col-xs-12 col-sm-4">
        <div class="form-group label-floating is-focused">
          <label class="control-label">Paciente (cédula / nombres) *</label>
          <select id="paciente_id" name="paciente_id" class="form-control" style="width:100%;" required></select>
        </div>
      </div>
      <div class="col-xs-12 col-sm-4">
        
        <div class="form-group label-floating is-focused">
          <label class="control-label">Sucursal *</label>
          <select class="form-control" id="sucursal_id_view" disabled>
            <option value="">
              <?php echo $sucursalActual ? htmlspecialchars($sucursalActual['nombre'],ENT_QUOTES,'UTF-8') : 'SELECCIONE'; ?>
            </option>
          </select>
          <input type="hidden" name="sucursal_id" id="sucursal_id" value="<?php echo htmlspecialchars($sucId,ENT_QUOTES,'UTF-8'); ?>">
        </div>
      </div>
      <div class="col-xs-12 col-sm-4" style="margin-top:24px;">
        <a href="<?php echo SERVERURL; ?>paciente/" class="btn btn-primary">
          <i class="zmdi zmdi-account-add"></i> Registrar nuevo paciente
        </a>
      </div>
    </div>

    <!-- Sucursal / Especialidad / Médico -->
    <div class="row">
      <div class="col-xs-12 col-sm-4">
        <div class="form-group label-floating is-focused">
          <label class="control-label">Estado *</label>
          <select name="estadoc" class="form-control" style="width:100%;" required>
            <option value="RESERVADO">Reservado</option>
            <option value="CONFIRMADA">Confirmado</option>
          </select>
        </div>
      </div>

      <div class="col-xs-12 col-sm-4">
        <div class="form-group label-floating is-focused">
          <label class="control-label">Especialidad *</label>
          <select class="form-control js-especialidad-select" id="especialidad_id" name="especialidad_id" required style="width:100%">
            <option value="">SELECCIONE</option>
            <?php if($espStmt && $espStmt->rowCount()>0): while($e=$espStmt->fetch(PDO::FETCH_ASSOC)){ ?>
              <option value="<?php echo (int)$e['id']; ?>">
                <?php echo htmlspecialchars($e['nombre'],ENT_QUOTES,'UTF-8'); ?>
              </option>
            <?php } endif; ?>
          </select>
        </div>
      </div>

      <div class="col-xs-12 col-sm-4">
        <div class="form-group label-floating is-focused">
          <label class="control-label">Médico *</label>
          <!-- Este select envía id_especialidad_med -->
          <select class="form-control" name="id_especialidad_med" id="id_especialidad_med" required disabled>
            <option value="">SELECCIONE</option>
          </select>
        </div>
      </div>
    </div>

    <small class="text-muted">
      1) Seleccione especialidad y médico. 2) Luego haga clic en un bloque de 30 minutos dentro del horario (09:00–17:00).
    </small>

    <!-- Calendario -->
    <div class="row">
      <div class="col-xs-12">
        <div id="calendar"></div>
        <div class="legend">
          <span><span class="dot dot-now"></span>Fecha actual</span>
          <span><span class="dot dot-reserved"></span>Reservado</span>
          <span><span class="dot dot-confirmed"></span>Confirmado</span>
        </div>
        <small class="text-muted">Horario: 09:00 a 17:00 · Duración por cita: 30 min.</small>
      </div>
    </div>

    <!-- Campos llenados por el calendario -->
    <input type="hidden" name="fecha" id="fecha">
    <input type="hidden" name="hora_inicio" id="hora_inicio">
    <input type="hidden" name="hora_fin" id="hora_fin">
    <input type="hidden" name="origen" value="DIRECTA">

    <p class="text-center" style="margin-top:15px;">
      <button id="btnGuardar" type="submit" class="btn btn-info btn-raised btn-sm" disabled>
        <i class="zmdi zmdi-floppy"></i> Guardar Cita
      </button>
    </p>
  </form>
</div>
<!-- Modal Detalle Cita -->
<div class="modal fade" id="modalCita" tabindex="-1" role="dialog" aria-labelledby="modalCitaLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalCitaLabel">Detalle de Cita</h4>
      </div>
      <div class="modal-body">
        <div id="detalle-cita-body">
          <!-- contenido se inyecta por JS -->
        </div>
      </div>
      <div class="modal-footer">
        <button id="btnEditarCita" type="button" class="btn btn-primary">Editar</button>
        <button id="btnCancelarCita" type="button" class="btn btn-danger">Cancelar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>




<script>
/* ——— helpers de UI para el modal ——— */
function resetDetalleUI() {
  // Estado base del modal de detalle
  const $m = $('#modalCita');
  $('.modal-title', $m).text('Detalle de Cita');
  $('#detalle-cita-body').html('<p>Cargando detalle...</p>');
  $('#btnEditarCita, #btnCancelarCita').show();
}

/* Asegura que el modal cuelgue de <body> y limpia backdrops viejos */
$('#modalCita').appendTo('body');
$('.modal-backdrop').remove();

/* Resetea SIEMPRE al abrir/cerrar */
$('#modalCita')
  .off('show.bs.modal shown.bs.modal hidden.bs.modal')
  .on('show.bs.modal', function(){ resetDetalleUI(); })
  .on('hidden.bs.modal', function(){
    $('#detalle-cita-body').empty();
    $('#btnEditarCita, #btnCancelarCita').show();
    $('.modal-title', this).text('Detalle de Cita');
    $('.modal-backdrop').remove();
  });

(function(){
  const AJAX  = '<?= rtrim(SERVERURL, "/"); ?>/ajax/ajaxAgenda.php';
  const SERVER= '<?= rtrim(SERVERURL, "/"); ?>/';
  const $btn  = $('#btnGuardar');
  const $esp  = $('#especialidad_id');
  const $med  = $('#id_especialidad_med');
  const $fecha= $('#fecha');
  const $hi   = $('#hora_inicio');
  const $hf   = $('#hora_fin');

  console.log('[AJAX URL]', AJAX);

  $.ajaxSetup({
    cache:false,
    error: function(xhr, status, err){
      console.error('[AJAX ERROR]', status, err, xhr.status, xhr.responseText);
    }
  });

  $.post(AJAX, { action:'diagnose' })
    .done(res => console.log('[diagnose OK]', res))
    .fail(xhr => {
      console.error('[diagnose FAIL]', xhr.status, xhr.responseText);
      alert('No se pudo conectar con ajaxAgenda.php ('+xhr.status+'). Revisa consola/Network.');
    });

  let pacienteOk=false, medicoOk=false, slotOk=false;
  function toggleGuardar(){ $btn.prop('disabled', !(pacienteOk && medicoOk && slotOk)); }

  // Select2
  $('.js-especialidad-select').select2({ placeholder:"Seleccione una especialidad", allowClear:true, width:'100%' });
  const $pac = $('#paciente_id');
  $pac.select2({
    placeholder:'Escriba cédula o nombre...',
    minimumInputLength:2,
    ajax:{
      url: SERVER+'ajax/ajaxAgenda.php',
      type:'POST', dataType:'json', delay:250, cache:false,
      data: params => ({ action:'buscar_pacientes_json', q: params.term }),
      beforeSend: xhr => xhr.setRequestHeader('Cache-Control','no-store'),
      processResults: data => ({ results: Array.isArray(data) ? data : [] })
    },
    language:{
      inputTooShort: ()=>"Escriba al menos 2 caracteres...",
      noResults: ()=>"Sin resultados",
      searching: ()=>"Buscando..."
    }
  });
  $pac.on('change', function(){ pacienteOk = !!$(this).val(); toggleGuardar(); });

  // Cargar médicos por especialidad
  $esp.on('change', function(){
    const espId = $(this).val();
    $med.val('').trigger('change');
    $med.prop('disabled', true).html('<option value="">Cargando...</option>');
    medicoOk=false; slotOk=false; toggleGuardar();
    $fecha.val(''); $hi.val(''); $hf.val('');
    if (typeof calendar !== 'undefined') calendar.removeAllEvents();

    if(!espId){
      $med.prop('disabled', true).html('<option value="">SELECCIONE</option>');
      if (typeof calendar !== 'undefined') calendar.refetchEvents();
      return;
    }

    $.post(SERVER+'ajax/ajaxAgenda.php', {
      action:'load_medicos', especialidad_id: espId, sucursal_id: $('#sucursal_id').val()
    }, function(html){
      $med.html(html).prop('disabled', false);
      if (typeof calendar !== 'undefined') calendar.refetchEvents();
    }).fail(function(xhr){
      console.error('load_medicos FAIL', xhr.status, xhr.responseText);
      $med.html('<option value="">Error cargando médicos</option>').prop('disabled', true);
    });
  });

  $med.on('change', function(){
    medicoOk = !!$(this).val();
    slotOk=false; toggleGuardar();
    $fecha.val(''); $hi.val(''); $hf.val('');
    if (typeof calendar !== 'undefined') calendar.refetchEvents();
  });

  // —— FullCalendar ——
  const today00 = new Date(); today00.setHours(0,0,0,0);

  let calendarEl = document.getElementById('calendar');
  let calendar = new FullCalendar.Calendar(calendarEl, {
    locale:'es',
    height:'auto',
    initialView:'timeGridWeek',
    firstDay:1,
    nowIndicator:true,
    timeZone:'local',
    headerToolbar:{ left:'today', center:'title', right:'prev,next' },
    businessHours:{ daysOfWeek:[1,2,3,4,5], startTime:'09:00', endTime:'17:00' },
    hiddenDays:[0,6],
    validRange:{ start: today00 },
    slotMinTime:'09:00:00',
    slotMaxTime:'17:00:00',
    slotDuration:'00:30:00',
    snapDuration:'00:30:00',
    slotLabelInterval:{ minutes:30 },
    slotLabelFormat:{ hour:'2-digit', minute:'2-digit', hour12:false },
    allDaySlot:false,
    selectable:true,
    unselectAuto:true,
    selectMirror:true,
    selectOverlap:false,

    eventDidMount: function(info){
      const est = (info.event.extendedProps?.estado || '').toUpperCase();
      const t = info.event.title || '';
      info.el.setAttribute('title', `${t} · Estado: ${est}`);
    },

    selectAllow: function(sel){
      if(!$med.val()) return false;
      const ms = sel.end.getTime() - sel.start.getTime();
      if(ms !== 30*60*1000) return false;
      const now = new Date();
      if (sel.start < now) return false;
      const isWeekday = d => { const dow=d.getDay(); return dow>=1 && dow<=5; };
      const hm = d => `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
      const within = (s,e)=> hm(s) >= '09:00' && hm(e) <= '17:00';
      if(!isWeekday(sel.start) || !isWeekday(sel.end)) return false;
      if(!within(sel.start, sel.end)) return false;
      return true;
    },

    select: function(info){
      const start = new Date(info.start), end = new Date(info.end);
      $fecha.val(`${start.getFullYear()}-${String(start.getMonth()+1).padStart(2,'0')}-${String(start.getDate()).padStart(2,'0')}`);
      $hi.val(`${String(start.getHours()).padStart(2,'0')}:${String(start.getMinutes()).padStart(2,'0')}`);
      $hf.val(`${String(end.getHours()).padStart(2,'0')}:${String(end.getMinutes()).padStart(2,'0')}`);

      $.post(SERVER+'ajax/ajaxAgenda.php', {
        action:'check_disponibilidad',
        id_especialidad_med:$med.val(),
        fecha:$fecha.val(),
        hora_inicio:$hi.val(),
        hora_fin:$hf.val()
      }, function(res){
        if(res && res.disponible){ slotOk=true; toggleGuardar(); }
        else{
          slotOk=false; toggleGuardar();
          swal("Conflicto de horario","Ya existe una cita en ese rango","warning");
          $fecha.val(''); $hi.val(''); $hf.val(''); calendar.unselect();
        }
      }, 'json').fail(function(){
        slotOk=false; toggleGuardar();
        swal("Error","No se pudo validar disponibilidad","error");
        $fecha.val(''); $hi.val(''); $hf.val(''); calendar.unselect();
      });
    },

    eventClick: function(info){
      info.jsEvent.preventDefault();
      const idCita = info.event.id;

      // fuerza estado base
      resetDetalleUI();
      $('.modal-backdrop').remove();
      $('#modalCita').modal({ backdrop:true, keyboard:true, show:true });

      $.ajax({
        url: SERVER+'ajax/ajaxAgenda.php',
        type:'POST', dataType:'json',
        data:{ action:'detalle_cita', id:idCita, debug:1 },
      })
      .done(function(res){
        if(!res){ $('#detalle-cita-body').html('<div class="alert alert-danger">Respuesta vacía</div>'); return; }
        if(res.ok === false){
          const msg = (res.error ? res.error : 'Error desconocido');
          $('#detalle-cita-body').html('<div class="alert alert-danger">'+msg+'</div>');
          return;
        }

        const fecha = (res.fecha || '');
        const rango = (res.hora_inicio && res.hora_fin) ? (res.hora_inicio+' - '+res.hora_fin) : (res.hora||'');

        const detalleHTML = `
          <div id="vista-detalle">
            <table class="table table-condensed">
              <tr><th>Estado</th><td>${(res.estado||'').toUpperCase()}</td></tr>
              <tr><th>Paciente</th><td>${res.paciente_nombre||''} ${res.paciente_apellido||''} ${res.paciente_cedula ? ' ('+res.paciente_cedula+')' : ''}</td></tr>
              <tr><th>Médico</th><td>${res.medico_nombre||''} ${res.medico_apellido||''}</td></tr>
              <tr><th>Especialidad</th><td>${res.especialidad||''}</td></tr>
              <tr><th>Fecha</th><td>${fecha}</td></tr>
              <tr><th>Hora</th><td>${rango}</td></tr>
              <tr><th>Sucursal</th><td>${res.sucursal||''}</td></tr>
              <tr><th>Creada por</th><td>${res.creada_por||''} ${res.creado_en ? ' · '+res.creado_en : ''}</td></tr>
            </table>
          </div>
          <div id="vista-cancelar" style="display:none;">
            <div class="form-group">
              <label>Razón de cancelación</label>
              <textarea id="cancelRazonInline" class="form-control" rows="3" placeholder="Ej: Paciente no se presentó"></textarea>
              <div id="cancelError" class="text-danger" style="display:none;margin-top:6px;"></div>
            </div>
            <div class="text-right">
              <button type="button" class="btn btn-default" id="btnCancelarVolver">Volver</button>
              <button type="button" class="btn btn-danger" id="btnCancelarConfirmar">Confirmar cancelación</button>
            </div>
          </div>
        `;
        $('#detalle-cita-body').html(detalleHTML);

        // Editar
        $('#btnEditarCita').off('click').on('click', function(){
          window.location.href = SERVER + 'citaeditar/' + idCita + '/';
        });

        // Ir a cancelar (vista inline)
        $('#btnCancelarCita').off('click').on('click', function(){
          $('#vista-detalle').hide();
          $('#vista-cancelar').show();
          $('#btnEditarCita, #btnCancelarCita').hide();
          $('.modal-title', '#modalCita').text('Cancelar cita');
          setTimeout(function(){ $('#cancelRazonInline').focus(); }, 150);
        });

        // Volver
        $('#detalle-cita-body').on('click', '#btnCancelarVolver', function(){
          $('#vista-cancelar').hide();
          $('#vista-detalle').show();
          $('#btnEditarCita, #btnCancelarCita').show();
          $('.modal-title', '#modalCita').text('Detalle de Cita');
        });

        // Confirmar cancelación
        $('#detalle-cita-body').on('click', '#btnCancelarConfirmar', function(){
          const razon = ($('#cancelRazonInline').val() || '').trim();
          if(!razon){ $('#cancelError').text('Debes ingresar una razón.').show(); return; }
          $('#cancelError').hide().text('');
          const $b = $(this).prop('disabled', true).text('Cancelando...');

          $.post(SERVER+'ajax/ajaxAgenda.php', { action:'cancelar_cita', id:idCita, razon:razon }, function(r){
            if(r && r.ok){
              (window.Swal&&Swal.fire) ? Swal.fire('OK','Cita cancelada','success') : (window.swal&&swal('OK','Cita cancelada','success'));
              $('#modalCita').modal('hide');
              calendar.refetchEvents();
            }else{
              const msg = (r && r.error) ? r.error : 'No se pudo cancelar';
              (window.Swal&&Swal.fire) ? Swal.fire('Error', msg, 'error') : (window.swal&&swal('Error', msg, 'error'));
              $b.prop('disabled', false).text('Confirmar cancelación');
            }
          }, 'json').fail(function(){
            (window.Swal&&Swal.fire) ? Swal.fire('Error','No se pudo cancelar','error') : (window.swal&&swal('Error','No se pudo cancelar','error'));
            $b.prop('disabled', false).text('Confirmar cancelación');
          });
        });
      })
      .fail(function(xhr){
        $('#detalle-cita-body').html('<div class="alert alert-danger">Error de comunicación ('+xhr.status+')</div>');
      });
    },

    // Cargar eventos
    events: function(fetchInfo, success, fail){
      const idEM = $med.val();
      if(!idEM){ success([]); return; }

      $.ajax({
        url: SERVER+'ajax/ajaxAgenda.php',
        type:'POST', dataType:'json', cache:false,
        headers:{ 'Cache-Control':'no-store' },
        data:{ action:'listar_citas', id_especialidad_med:idEM, start:fetchInfo.startStr, end:fetchInfo.endStr },
        success: function(res){
          try{
            const arr = (typeof res === 'string') ? JSON.parse(res) : res;
            const evs = (arr || []).map(e => {
              const est = (e.extendedProps && e.extendedProps.estado ? e.extendedProps.estado : '').toUpperCase();
              e.classNames = (e.classNames || []).filter(c => c!=='reservado' && c!=='estado-reservada' && c!=='estado-confirmada' && c!=='estado-atendida');
              if(est === 'RESERVADO')   e.classNames.push('reservado','estado-reservada');
              else if(est === 'CONFIRMADA') e.classNames.push('estado-confirmada');
              else if(est === 'ATENDIDA')   e.classNames.push('estado-atendida');
              return e;
            });
            success(evs);
          }catch(err){
            console.error('JSON parse error events:', err, res);
            success([]);
          }
        },
        error: function(xhr){
          console.error('events FAIL', xhr.status, xhr.responseText);
          fail(xhr);
        }
      });
    }
  });

  calendar.render();

  // Guardado por AJAX
  $('#formCita').on('submit', function(e){
    if($btn.prop('disabled')){
      e.preventDefault();
      swal("Atención","Debe seleccionar paciente, médico y un horario disponible","warning");
      return;
    }
    e.preventDefault();
    $btn.prop('disabled', true).text('Guardando...');

    $.post(window.location.href, $(this).serialize())
      .done(function(html){
        if(/Cita registrada/i.test(html)){
          swal("OK","La cita se registró con éxito","success");
          calendar.refetchEvents();
          $('#hora_inicio,#hora_fin,#fecha').val('');
          slotOk=false; toggleGuardar();
          calendar.unselect();
        }else{
          swal("Aviso","No se pudo confirmar el guardado. Verifique mensajes.","warning");
          console.log('RESPUESTA SERVIDOR:', html);
        }
      })
      .fail(function(xhr){
        swal("Error","No se pudo registrar la cita","error");
        console.error('submit FAIL', xhr.status, xhr.responseText);
      })
      .always(function(){
        $btn.prop('disabled', false).text('Guardar Cita');
      });
  });

})(); // fin IIFE

/* ——— IMPORTANTE: se eliminó el submit de #formCancelarCita porque ya no existe ——— */
</script>



<?php
else:
  $logout2 = new loginController();
  echo $logout2->login_session_force_destroy_controller();
endif;
