<?php
if(isset($_SESSION['userType']) && $_SESSION['userType']==="Secretaria"):
  date_default_timezone_set('America/Guayaquil');
  require_once "./controllers/agendaController.php";
  require_once "./controllers/pacienteController.php";
  $agendaCtrl = new agendaController();
  $pc        = new pacienteController();
  $id_sucursal=$_SESSION['userIdSuc'];

  if(
    isset($_POST['paciente_id'], $_POST['sucursal_id'], $_POST['id_especialidad_med'],
          $_POST['fecha'], $_POST['hora_inicio'], $_POST['hora_fin'])
  ){
    
      # code...
        echo $agendaCtrl->add_cita_controller();

    
  }

  $sucId = $_SESSION['userIdSuc'];
  $sucStmt = $pc->execute_single_query("SELECT id_suc, nombre FROM sucursales WHERE id_suc='$sucId' LIMIT 1");
  $sucursalActual = $sucStmt && $sucStmt->rowCount()>0 ? $sucStmt->fetch(PDO::FETCH_ASSOC) : null;

  $espStmt = $pc->execute_single_query("SELECT id, nombre FROM especialidades WHERE estado='Activa' ORDER BY nombre ASC");
?>
<style>
/* ===== Stacking y layout ===== */
#calendar, .fc, .container-fluid{ position:relative; z-index:1; }

/* Modales SIEMPRE arriba */
.modal         { z-index: 200000 !important; }
.modal-backdrop{ z-index: 199990 !important; }
/* SweetAlert2 arriba del modal si lo usas */
.swal2-container{ z-index: 210000 !important; }

/* FullCalendar */
.fc-toolbar-title { font-size: 1.6rem; }
.fc-timegrid-slot { height: 2.2em; }
/* Evita clipping del modal si el calendario crea contenedor con overflow */
.fc .fc-scroller-harness, .fc .fc-scroller{ overflow: visible !important; }

/* Leyenda */
.legend { margin-top: 10px; display:flex; gap:18px; align-items:center; flex-wrap:wrap; }
.legend .dot { width:14px; height:14px; display:inline-block; border-radius:3px; margin-right:6px; }
.dot-now       { background:#5bc0de; }
.dot-confirmed { background:#5cb85c; }  /* verde = CONFIRMADA */
.dot-reserved  { background:#d9534f; }  /* rojo  = RESERVADO */
.dot-waitlist  { background:#f0ad4e; }  /* naranja = LISTA_ESPERA */

/* Mapeo de clases a colores del calendario */
.fc-event.estado-confirmada,
.fc-timegrid-event.estado-confirmada{
  background:#5cb85c !important; border-color:#5cb85c !important; color:#fff !important;
}
.fc-event.estado-reservada,
.fc-timegrid-event.estado-reservada{
  background:#d9534f !important; border-color:#d9534f !important; color:#fff !important;
}
.fc-event.estado-lista-espera,
.fc-timegrid-event.estado-lista-espera{
  background:#f0ad4e !important; border-color:#f0ad4e !important; color:#212529 !important;
}

/* Select2 */
.select2-container--default .select2-selection--single { height: 38px; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }

/* Indicador "ahora" */
.fc-now-indicator-line { border-top: 2px solid #5bc0de; }
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

    <div class="row">
      <div class="col-xs-12 col-sm-4">
        <div class="form-group label-floating is-focused">
          <label class="control-label">Estado *</label>
          <select name="estadoc" class="form-control" style="width:100%;" required>
            <option value="RESERVADO">Reservado</option>
            <option value="CONFIRMADA">Confirmado</option>
            <option value="LISTA_ESPERA" style="display:none;">Lista de espera</option>
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
          <select class="form-control" name="id_especialidad_med" id="id_especialidad_med" required disabled>
            <option value="">SELECCIONE</option>
          </select>
        </div>
      </div>
    </div>

    <small class="text-muted">
      1) Seleccione especialidad y médico. 2) Luego haga clic en un bloque de 30 minutos dentro del horario (09:00–17:00).
    </small>

    <div class="row">
      <div class="col-xs-12">
        <div id="calendar"></div>
        <div class="legend">
          <span><span class="dot dot-now"></span>Fecha actual</span>
          <span><span class="dot dot-confirmed"></span>Confirmado</span>
          <span><span class="dot dot-reserved"></span>Reservado</span>
          <span><span class="dot dot-waitlist"></span>Lista de espera</span>
        </div>
        <small class="text-muted">Horario: 09:00 a 17:00 · Duración por cita: 30 min.</small>
      </div>
    </div>

    <input type="hidden" name="fecha" id="fecha">
    <input type="hidden" name="hora_inicio" id="hora_inicio">
    <input type="hidden" name="hora_fin" id="hora_fin">
    <input type="hidden" name="origen" value="DIRECTA">
    <input type="hidden" name="force_estado" id="force_estado" value="">

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
      <div class="modal-body"><div id="detalle-cita-body"></div></div>
      <div class="modal-footer">
        <button id="btnEditarCita" type="button" class="btn btn-primary">Editar</button>
        <button id="btnCancelarCita" type="button" class="btn btn-danger">Cancelar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Acciones sobre Evento -->
<div class="modal fade" id="modalEventoAccion" tabindex="-1" role="dialog" aria-labelledby="modalEventoAccionLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalEventoAccionLabel">Acciones</h4>
      </div>
      <div class="modal-body">
        <p id="accionResumen" class="text-muted" style="margin-bottom:15px;"></p>
        <div class="text-right">
          <button type="button" id="btnAccionVer" class="btn btn-primary">Ver detalle</button>
          <!-- Para eventos NO-WL -->
          <button type="button" id="btnAccionWL" class="btn btn-warning">Agregar a lista de espera</button>
          <!-- Para eventos WL -->
          <button type="button" id="btnAccionWLConfirm" class="btn btn-success" style="display:none;">Confirmar cita</button>
          <button type="button" id="btnAccionWLCancel" class="btn btn-danger" style="display:none;">Cancelar</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
// Garantiza que los modales cuelguen de <body>
$('#modalCita, #modalEventoAccion').appendTo('body');

// Asegura que el backdrop también cuelgue de <body> (corrige stacking)
$(document).on('show.bs.modal', '.modal', function () {
  setTimeout(function(){ $('.modal-backdrop').appendTo(document.body); }, 0);
});

// Limpia posibles backdrops duplicados al cerrar
$(document).on('hidden.bs.modal', '.modal', function () {
  $('body').removeClass('modal-open');
  $('.modal-backdrop').remove();
});

// ==== URLS GLOBALES (usadas dentro y fuera del IIFE) ====
const SERVER = '<?= rtrim(SERVERURL, "/"); ?>/';
const AJAX   = SERVER + 'ajax/ajaxAgenda.php';

// ===== estado global del evento clicado
let currentEvent = null;

// ===== Modal acciones (ver / WL / confirmar / cancelar)
function abrirModalAccion(event){
  currentEvent = event;
  const est = (event.extendedProps?.estado || '').toUpperCase();
  const st = event.start, en = event.end || new Date(st.getTime()+30*60000);
  const rango = (st ? st.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) : '') + ' - ' +
                (en ? en.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) : '');

  document.getElementById('accionResumen').innerHTML =
    `<b>Estado:</b> ${est} &nbsp;·&nbsp; <b>Horario:</b> ${rango}`;

  const btnWL     = document.getElementById('btnAccionWL');
  const btnConfirm= document.getElementById('btnAccionWLConfirm');
  const btnCancel = document.getElementById('btnAccionWLCancel');

  btnWL.textContent = 'Agregar cita a lista de espera'; // <- crea NUEVA WL (no toca la ocupante)

  if(est === 'LISTA_ESPERA'){
    btnWL.style.display = 'none';
    btnConfirm.style.display = '';
    btnCancel.style.display = '';
  }else{
    btnWL.style.display = '';
    btnConfirm.style.display = 'none';
    btnCancel.style.display = 'none';
  }

  $('#modalEventoAccion').appendTo('body').modal({backdrop:true, keyboard:true, show:true});
}

// Click en "Agregar cita a lista de espera" (crea NUEVA WL con el paciente del form)
$('#btnAccionWL').off('click').on('click', function(){
  if(!currentEvent){ return; }
  $('#modalEventoAccion').modal('hide');
  crearNuevaWLDesdeEvento(currentEvent);  // <- NUEVA función
});

// handlers de los botones del modal de acción
$('#btnAccionVer').off('click').on('click', function(){
  if(!currentEvent){ return; }
  $('#modalEventoAccion').modal('hide');
  abrirDetalleCita(currentEvent.id);
});



// Confirmar y cancelar cuando el evento es LISTA_ESPERA
$('#btnAccionWLConfirm').off('click').on('click', function(){
  if(!currentEvent){ return; }
  $('#modalEventoAccion').modal('hide');
  confirmarDesdeWaitlist(currentEvent.id); // <- SOLO ID
});
$('#btnAccionWLCancel').off('click').on('click', function(){
  if(!currentEvent){ return; }
  $('#modalEventoAccion').modal('hide');
  pedirCancelacion(currentEvent.id);
});

// ===== helpers UI detalle
function resetDetalleUI() {
  const $m = $('#modalCita');
  $('.modal-title', $m).text('Detalle de Cita');
  $('#detalle-cita-body').html('<p>Cargando detalle...</p>');
  $('#btnEditarCita, #btnCancelarCita').show();
}

$('#modalCita').appendTo('body');
$('.modal-backdrop').remove();
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
  const $btn  = $('#btnGuardar');
  const $esp  = $('#especialidad_id');
  const $med  = $('#id_especialidad_med');
  const $fecha= $('#fecha');
  const $hi   = $('#hora_inicio');
  const $hf   = $('#hora_fin');

  $.ajaxSetup({ cache:false, error: (xhr,s,err)=>console.error('[AJAX ERROR]', s, err, xhr.status, xhr.responseText) });
  $.post(AJAX, { action:'diagnose' });

  let pacienteOk=false, medicoOk=false, slotOk=false;
  function toggleGuardar(){ $btn.prop('disabled', !(pacienteOk && medicoOk && slotOk)); }

  $('.js-especialidad-select').select2({ placeholder:"Seleccione una especialidad", allowClear:true, width:'100%' });
  const $pac = $('#paciente_id');
  $pac.select2({
    placeholder:'Escriba cédula o nombre...', minimumInputLength:2,
    ajax:{
      url: AJAX, type:'POST', dataType:'json', delay:250, cache:false,
      data: p => ({ action:'buscar_pacientes_json', q: p.term }),
      processResults: data => ({ results: Array.isArray(data) ? data : [] })
    }
  });
  $pac.on('change', function(){ pacienteOk = !!$(this).val(); toggleGuardar(); });

  $esp.on('change', function(){
    const espId = $(this).val();
    $med.val('').trigger('change').prop('disabled', true).html('<option value="">Cargando...</option>');
    medicoOk=false; slotOk=false; toggleGuardar();
    $fecha.val(''); $hi.val(''); $hf.val('');
    if (typeof calendar !== 'undefined') calendar.removeAllEvents();
    if(!espId){ $med.prop('disabled', true).html('<option value="">SELECCIONE</option>'); if (typeof calendar!=='undefined') calendar.refetchEvents(); return; }

    $.post(AJAX, { action:'load_medicos', especialidad_id: espId, sucursal_id: $('#sucursal_id').val() },
      html => { $med.html(html).prop('disabled', false); if (typeof calendar!=='undefined') calendar.refetchEvents(); }
    ).fail(xhr => { console.error('load_medicos FAIL', xhr.status, xhr.responseText); $med.html('<option value="">Error cargando médicos</option>').prop('disabled', true); });
  });

  $med.on('change', function(){
    medicoOk = !!$(this).val();
    slotOk=false; toggleGuardar();
    $fecha.val(''); $hi.val(''); $hf.val('');
    if (typeof calendar !== 'undefined') calendar.refetchEvents();
  });

  const today00 = new Date(); today00.setHours(0,0,0,0);
  let calendarEl = document.getElementById('calendar');
  let calendar = new FullCalendar.Calendar(calendarEl, {
    locale:'es', height:'auto', initialView:'timeGridWeek', firstDay:1, nowIndicator:true, timeZone:'local',
    headerToolbar:{ left:'today', center:'title', right:'prev,next' },
    businessHours:{ daysOfWeek:[1,2,3,4,5], startTime:'09:00', endTime:'17:00' },
    hiddenDays:[0,6], validRange:{ start: today00 }, slotMinTime:'09:00:00', slotMaxTime:'17:00:00',
    slotDuration:'00:30:00', snapDuration:'00:30:00', slotLabelInterval:{ minutes:30 },
    slotLabelFormat:{ hour:'2-digit', minute:'2-digit', hour12:false }, allDaySlot:false,
    selectable:true, unselectAuto:true, selectMirror:true, selectOverlap:false,

    eventDidMount: function(info){
      const est = (info.event.extendedProps?.estado || '').toUpperCase();
      info.el.setAttribute('title', `${info.event.title||''} · Estado: ${est}`);
    },

    selectAllow: function(sel){
      if(!$med.val()) return false;
      const ms = sel.end.getTime() - sel.start.getTime();
      if(ms !== 30*60*1000) return false;
      if (sel.start < new Date()) return false;
      const dow = sel.start.getDay(); if(dow===0||dow===6) return false;
      const hm = d => `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
      return hm(sel.start) >= '09:00' && hm(sel.end) <= '17:00';
    },

    select: function(info){
      const start = new Date(info.start), end = new Date(info.end);
      $fecha.val(`${start.getFullYear()}-${String(start.getMonth()+1).padStart(2,'0')}-${String(start.getDate()).padStart(2,'0')}`);
      $hi.val(`${String(start.getHours()).padStart(2,'0')}:${String(start.getMinutes()).padStart(2,'0')}`);
      $hf.val(`${String(end.getHours()).padStart(2,'0')}:${String(end.getMinutes()).padStart(2,'0')}`);

      $.post(AJAX, {
        action:'check_disponibilidad', id_especialidad_med:$med.val(), fecha:$fecha.val(), hora_inicio:$hi.val(), hora_fin:$hf.val()
      }, function(res){
        if(res && res.disponible){
          slotOk=true; toggleGuardar();
        } else {
          slotOk=false; toggleGuardar();
          if (window.Swal && Swal.fire) {
            Swal.fire({
              icon:'info', title:'Sin disponibilidad',
              html:"<p>Ese horario ya tiene una cita <b>reservada o confirmada</b>.</p><p>¿Desea agregar al paciente a <b>lista de espera</b> para esta fecha y hora <i>estimadas</i>?</p>",
              showCancelButton:true, confirmButtonText:'Agregar a lista de espera', cancelButtonText:'Cerrar'
            }).then(r=>{ if(r.isConfirmed){ crearCitaListaEspera(); } else { $('#fecha,#hora_inicio,#hora_fin').val(''); calendar.unselect(); }});
          } else {
            if(confirm("Sin disponibilidad. ¿Agregar a lista de espera para este horario estimado?")) crearCitaListaEspera();
            $('#fecha,#hora_inicio,#hora_fin').val(''); calendar.unselect();
          }
        }
      }, 'json').fail(function(){
        slotOk=false; toggleGuardar();
        swal("Error","No se pudo validar disponibilidad","error");
        $('#fecha,#hora_inicio,#hora_fin').val(''); calendar.unselect();
      });
    },

    eventClick: function(info){
      info.jsEvent.preventDefault();
      abrirModalAccion(info.event);
    },

    events: function(fetchInfo, success, fail){
      const idEM = $med.val(); if(!idEM){ success([]); return; }
      $.ajax({
        url: AJAX, type:'POST', dataType:'json', cache:false,
        headers:{ 'Cache-Control':'no-store' },
        data:{ action:'listar_citas', id_especialidad_med:idEM, start:fetchInfo.startStr, end:fetchInfo.endStr },
        success: function(res){
          try{
            const arr = (typeof res === 'string') ? JSON.parse(res) : res;
            const evs = (arr || []).map(e => {
              const est = (e.extendedProps && e.extendedProps.estado ? e.extendedProps.estado : '').toUpperCase();
              // Limpia clases previas
              e.classNames = (e.classNames || []).filter(c =>
                c!=='estado-reservada' && c!=='estado-confirmada' &&
                c!=='estado-atendida' && c!=='estado-lista-espera'
              );
              // Mapeo a colores:
              if(est === 'RESERVADO')        e.classNames.push('estado-reservada');      // rojo
              else if(est === 'CONFIRMADA')  e.classNames.push('estado-confirmada');     // verde
              else if(est === 'ATENDIDA')    e.classNames.push('estado-atendida');
              else if(est === 'LISTA_ESPERA')e.classNames.push('estado-lista-espera');   // naranja
              return e;
            });
            success(evs);
          }catch(err){ console.error('JSON parse error events:', err, res); success([]); }
        },
        error: function(xhr){ console.error('events FAIL', xhr.status, xhr.responseText); fail(xhr); }
      });
    }
  });

  window.calendar = calendar;

  calendar.render();

  // submit normal
  $('#formCita').on('submit', function(e){
    if($btn.prop('disabled')){ e.preventDefault(); swal("Atención","Debe seleccionar paciente, médico y un horario disponible","warning"); return; }
    e.preventDefault();
    $btn.prop('disabled', true).text('Guardando...');
    $.post(window.location.href, $(this).serialize())
      .done(function(html){
        if(/Cita registrada/i.test(html)){
          swal("OK","La cita se registró con éxito","success");
          calendar.refetchEvents();
          $('#hora_inicio,#hora_fin,#fecha,#force_estado').val('');
          slotOk=false; toggleGuardar(); calendar.unselect();
        }else{
          swal("Aviso","No se pudo confirmar el guardado. Verifique mensajes.","warning");
          console.log('RESPUESTA SERVIDOR:', html);
        }
      })
      .fail(function(xhr){ swal("Error","No se pudo registrar la cita","error"); console.error('submit FAIL', xhr.status, xhr.responseText); })
      .always(function(){ $btn.prop('disabled', false).text('Guardar Cita'); });
  });

  // crear WL desde selección (si el slot está ocupado, se envía id_cita para actualizar)
  function crearCitaListaEspera(){
    const pac=$('#paciente_id').val(), suc=$('#sucursal_id').val(), idEM=$('#id_especialidad_med').val();
    const slot = getSlotFromEventOrForm(null);
    if(!pac||!suc||!idEM||!slot){ swal('Faltan datos','Seleccione paciente, médico y un rango horario estimado','warning'); return; }

    findExistingCitaId(idEM, slot).then((idCitaOcupante)=>{
      const formData = $('#formCita').serializeArray();
      formData.push({ name:'id_cita', value:(idCitaOcupante||'') }); // si hay ocupante, actualiza
      formData.push({ name:'estadoc', value:'LISTA_ESPERA' });
      formData.push({ name:'force_estado', value:'LISTA_ESPERA' });
      formData.push({ name:'origen', value:'LISTA_ESPERA' });

      $.post(window.location.href, $.param(formData))
        .done(function(html){
          if(/(LISTA_ESPERA|Cita registrada|actualizada|actualizado)/i.test(html)){
            swal('OK','Agregado a lista de espera','success');
            if(window.calendar){ calendar.unselect(); calendar.refetchEvents(); }
            $('#hora_inicio,#hora_fin,#fecha,#force_estado').val('');
          }else{
            swal('Aviso','No se pudo confirmar el guardado.','warning');
            console.log('RESPUESTA SERVIDOR (WL):', html);
          }
        })
        .fail(()=>swal('Error','No se pudo registrar en lista de espera','error'));
    });
  }

  // WL desde evento ocupado
  window.agregarListaEsperaDesdeEvento = function(event){
    const pacId=$('#paciente_id').val(), idEM=$('#id_especialidad_med').val(), suc=$('#sucursal_id').val();
    if(!pacId){ swal('Faltan datos','Seleccione un paciente en el formulario.','warning'); return; }
    if(!idEM){ swal('Faltan datos','Seleccione el médico en el formulario.','warning'); return; }
    if(!suc){  swal('Faltan datos','No se detecta la sucursal.','warning'); return; }

    const slot = getSlotFromEventOrForm(event);
    if(!slot){ swal('Faltan datos','No se pudo determinar fecha/hora.','warning'); return; }

    // Regla: solo 1 WL por médico+horario
    hasWaitlistForSlot(idEM, slot).then(existeWL=>{
      if(existeWL){
        swal('Aviso','Ya existe una lista de espera para ese horario.','info');
        return;
      }

      // Creamos WL nueva, referenciando la cita ocupante (event.id)
      const formData = [
        {name:'id_cita', value: event.id || ''},   // referenciamos ocupante
        {name:'paciente_id', value: pacId},
        {name:'sucursal_id', value: suc},
        {name:'id_especialidad_med', value: idEM},
        {name:'fecha', value: slot.fecha},
        {name:'hora_inicio', value: slot.hi},
        {name:'hora_fin', value: slot.hf},
        {name:'estadoc', value:'LISTA_ESPERA'},
        {name:'force_estado', value:'LISTA_ESPERA'},
        {name:'origen', value:'LISTA_ESPERA'}
      ];

      $.post(window.location.href, $.param(formData))
        .done(function(html){
          if(/(LISTA_ESPERA|Cita registrada|actualizada|actualizado)/i.test(html)){
            swal('OK','Paciente agregado a lista de espera.','success');
            if(window.calendar) window.calendar.refetchEvents();
          }else{
            swal('Aviso','No se pudo registrar en lista de espera.','warning');
            console.log('RESPUESTA (WL desde evento):', html);
          }
        })
        .fail(()=>swal('Error','No se pudo registrar en lista de espera','error'));
    });
  

    // Si NO es RESERVADO/CONFIRMADA (hueco) → crear WL vinculada al ocupante si existe
    findExistingCitaId(idEM, slot).then((idCitaOcupante)=>{
      const formData = [
        {name:'id_cita', value: idCitaOcupante || ''}, // si hay cita dueña del slot, se pasa
        {name:'paciente_id', value: pacId},
        {name:'sucursal_id', value: suc},
        {name:'id_especialidad_med', value: idEM},
        {name:'fecha', value: slot.fecha},
        {name:'hora_inicio', value: slot.hi},
        {name:'hora_fin', value: slot.hf},
        {name:'estadoc', value:'LISTA_ESPERA'},
        {name:'force_estado', value:'LISTA_ESPERA'},
        {name:'origen', value:'LISTA_ESPERA'}
      ];
      $.post(window.location.href, $.param(formData))
        .done(function(html){
          if(/(LISTA_ESPERA|Cita registrada)/i.test(html)){
            swal('OK','Paciente agregado a lista de espera.','success');
            if(window.calendar) calendar.refetchEvents();
          }else{
            swal('Aviso','No se pudo registrar en lista de espera.','warning');
            console.log('RESPUESTA (WL desde evento):', html);
          }
        })
        .fail(()=>swal('Error','No se pudo registrar en lista de espera','error'));
    });
  };

})(); // fin IIFE

// ===== Detalle de cita (fuera del IIFE, usa AJAX global)
function abrirDetalleCita(idCita){
  resetDetalleUI();
  $('.modal-backdrop').remove();
  $('#modalCita').appendTo('body').modal({ backdrop:true, keyboard:true, show:true });

  $.ajax({ url: AJAX, type:'POST', dataType:'json', data:{ action:'detalle_cita', id:idCita } })
  .done(function(res){
    if(!res){ $('#detalle-cita-body').html('<div class="alert alert-danger">Respuesta vacía</div>'); return; }
    if(res.ok === false){ $('#detalle-cita-body').html('<div class="alert alert-danger">'+(res.error||'Error')+'</div>'); return; }

    const estado = (res.estado||'').toUpperCase();
    const fecha  = (res.fecha || '');
    const rango  = (res.hora_inicio && res.hora_fin) ? (res.hora_inicio+' - '+res.hora_fin) : (res.hora||'');

    let extraBtns = '';
    if(estado === 'LISTA_ESPERA'){
      extraBtns = `
        <div class="text-right" style="margin-top:12px">
          <button type="button" id="btnConfirmarDesdeWL" class="btn btn-success">Confirmar cita</button>
          <button type="button" id="btnCancelarDesdeWL" class="btn btn-danger">Cancelar</button>
        </div>`;
      $('#btnEditarCita').hide();
    } else if(estado === 'RESERVADO' || estado === 'CONFIRMADA'){
      // Botón para ACTUALIZAR esta misma cita a WL (solo desde detalle)
      extraBtns = `
        <div class="text-right" style="margin-top:12px">
          <button type="button" id="btnPasarAWaitlist" class="btn btn-warning">Pasar esta cita a LISTA DE ESPERA</button>
          <button type="button" id="btnCancelarCitaDet" class="btn btn-danger">Cancelar</button>
        </div>`;
    } else {
      $('#btnEditarCita').show().off('click').on('click', function(){ window.location.href = SERVER + 'citaeditar/' + idCita + '/'; });
    }

    const detalleHTML = `
      <table class="table table-condensed">
        <tr><th>Estado</th><td>${estado}</td></tr>
        <tr><th>Paciente</th><td>${res.paciente_nombre||''} ${res.paciente_apellido||''} ${res.paciente_cedula ? ' ('+res.paciente_cedula+')' : ''}</td></tr>
        <tr><th>Médico</th><td>${res.medico_nombre||''} ${res.medico_apellido||''}</td></tr>
        <tr><th>Especialidad</th><td>${res.especialidad||''}</td></tr>
        <tr><th>Fecha</th><td>${fecha}</td></tr>
        <tr><th>Hora</th><td>${rango}</td></tr>
        <tr><th>Sucursal</th><td>${res.sucursal||''}</td></tr>
        <tr><th>Creada por</th><td>${res.creada_por||''} ${res.creado_en ? ' · '+res.creado_en : ''}</td></tr>
        ${res.nota ? `<tr><th>Nota</th><td>${res.nota}</td></tr>` : ''}
      </table>${extraBtns}`;
    $('#detalle-cita-body').html(detalleHTML);

    // Cancelar desde detalle (btn rojo alternativo)
    $('#btnCancelarCita, #btnCancelarCitaDet').off('click').on('click', function(){ pedirCancelacion(idCita); });

    // Si es WL → confirmar/cancelar
    if(estado === 'LISTA_ESPERA'){
      $('#btnCancelarDesdeWL').off('click').on('click', function(){ pedirCancelacion(idCita); });
      $('#btnConfirmarDesdeWL').off('click').on('click', function(){ confirmarDesdeWaitlist(idCita); });
    }

    // Si es RES/CONF → pasar ESTA cita a WL (actualiza)
    $('#btnPasarAWaitlist').off('click').on('click', function(){
      // (Opcional) Evitar doble WL por médico+slot:
      // hasWaitlistForSlot(res.id_especialidad_med, { fecha: res.fecha, hi: res.hora_inicio, hf: res.hora_fin })
      //   .then(existe=>{ if(existe){ swal('Aviso','Ya existe WL en ese horario.','info'); return; } ... });

      $.post(AJAX, { action:'pasar_a_lista_espera', id: idCita }, function(r){
        if(r && r.ok){
          swal('OK','La cita pasó a LISTA DE ESPERA.','success');
          $('#modalCita').modal('hide');
          if(window.calendar) window.calendar.refetchEvents();
        }else{
          swal('Error', (r && r.error) || 'No se pudo actualizar la cita','error');
        }
      }, 'json').fail(function(){
        swal('Error','No se pudo actualizar la cita','error');
      });
    });
  })
  .fail(function(xhr){ $('#detalle-cita-body').html('<div class="alert alert-danger">Error de comunicación ('+xhr.status+')</div>'); });
}

function pedirCancelacion(idCita){
  const razon = prompt('Razón de cancelación (obligatoria):','');
  if(razon===null) return;
  if(!razon.trim()){ alert('Debes ingresar una razón.'); return; }

  $.post(AJAX, { action:'cancelar_cita', id:idCita, razon:razon }, function(r){
    if(r && r.ok){ swal('OK','Cita cancelada','success'); $('#modalCita').modal('hide'); if(window.calendar) calendar.refetchEvents(); }
    else{ swal('Error', (r && r.error) || 'No se pudo cancelar', 'error'); }
  }, 'json').fail(function(){ swal('Error','No se pudo cancelar','error'); });
}

/* Confirmar WL: ahora SOLO envía el id al backend,
   el backend valida disponibilidad y confirma. */
/* Confirmar una cita en LISTA_ESPERA:
   - Enviamos SOLO el id
   - El backend valida y confirma
*/
function confirmarDesdeWaitlist(idCita){
  if(!idCita){ swal('Aviso','ID de cita inválido.','warning'); return; }
  $.post(AJAX, { action:'confirmar_desde_espera', id:idCita }, function(r){
    if(r && r.ok){
      swal('OK','Cita confirmada','success');
      $('#modalCita').modal('hide');
      if(window.calendar) calendar.refetchEvents();
    }else{
      swal('Error', (r && r.error) || 'No se pudo confirmar','error');
    }
  }, 'json').fail(function(){
    swal('Error','No se pudo confirmar','error');
  });
}

</script>

<script>
  // === NUEVA: crear NUEVA WL a partir de un evento RES/CONF, sin modificar la ocupante ===
function crearNuevaWLDesdeEvento(event){
  const pacId=$('#paciente_id').val(), idEM=$('#id_especialidad_med').val(), suc=$('#sucursal_id').val();
  if(!pacId){ swal('Faltan datos','Seleccione un paciente en el formulario.','warning'); return; }
  if(!idEM){ swal('Faltan datos','Seleccione el médico en el formulario.','warning'); return; }
  if(!suc){  swal('Faltan datos','No se detecta la sucursal.','warning'); return; }

  const slot = getSlotFromEventOrForm(event);
  if(!slot){ swal('Faltan datos','No se pudo determinar fecha/hora.','warning'); return; }

  hasWaitlistForSlot(idEM, slot).then(existeWL=>{
    if(existeWL){ swal('Aviso','Ya existe una lista de espera para ese horario.','info'); return; }

    // OJO: NO mandamos id_cita para no modificar la existente
    const formData = [
      {name:'paciente_id', value: pacId},
      {name:'sucursal_id', value: suc},
      {name:'id_especialidad_med', value: idEM},
      {name:'fecha', value: slot.fecha},
      {name:'hora_inicio', value: slot.hi},
      {name:'hora_fin', value: slot.hf},
      {name:'estadoc', value:'LISTA_ESPERA'},
      {name:'force_estado', value:'LISTA_ESPERA'},
      {name:'origen', value:'LISTA_ESPERA'}
    ];

    $.post(window.location.href, $.param(formData))
      .done(function(html){
        if(/(LISTA_ESPERA|Cita registrada)/i.test(html)){
          swal('OK','Paciente agregado a lista de espera.','success');
          if(window.calendar) window.calendar.refetchEvents();
        }else{
          swal('Aviso','No se pudo registrar en lista de espera.','warning');
          console.log('RESPUESTA (crear WL):', html);
        }
      })
      .fail(()=>swal('Error','No se pudo registrar en lista de espera','error'));
  });
}

// --- Helpers: slot e id de cita existente ---
function getSlotFromEventOrForm(event){
  if(event && event.start){
    const d = event.start, e = event.end || new Date(d.getTime()+30*60000);
    const fecha = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    const hi    = `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
    const hf    = `${String(e.getHours()).padStart(2,'0')}:${String(e.getMinutes()).padStart(2,'0')}`;
    return { fecha, hi, hf };
  }
  const fecha = $('#fecha').val(), hi=$('#hora_inicio').val(), hf=$('#hora_fin').val();
  if(fecha && hi && hf) return { fecha, hi, hf };
  return null;
}

function hasWaitlistForSlot(idEM, slot){
  return new Promise((resolve)=>{
    if(!idEM || !slot){ resolve(false); return; }
    $.post(AJAX, {
      action:'listar_citas',
      id_especialidad_med:idEM,
      start: `${slot.fecha}T00:00:00`,
      end:   `${slot.fecha}T23:59:59`
    }, function(res){
      try{
        const arr = (typeof res==='string') ? JSON.parse(res) : (res||[]);
        const match = arr.find(e=>{
          const est = (e.extendedProps?.estado || '').toUpperCase();
          const ei  = e.extendedProps?.hora_inicio || (e.start?.substring(11,16) || '');
          const ef  = e.extendedProps?.hora_fin    || (e.end  ?.substring(11,16) || '');
          return est==='LISTA_ESPERA' && ei===slot.hi && ef===slot.hf;
        });
        resolve(!!match);
      }catch(_){ resolve(false); }
    }, 'json').fail(()=>resolve(false));
  });
}


/* Busca en el día una cita existente (no LISTA_ESPERA) que coincida en hora_inicio/hora_fin.
   Devuelve el id de la cita si la encuentra; si no, null. */
function findExistingCitaId(idEM, slot){
  return new Promise((resolve)=>{
    if(!idEM || !slot){ resolve(null); return; }
    $.post(AJAX, {
      action:'listar_citas',
      id_especialidad_med:idEM,
      start: `${slot.fecha}T00:00:00`,
      end:   `${slot.fecha}T23:59:59`
    }, function(res){
      try{
        const arr = (typeof res==='string') ? JSON.parse(res) : (res||[]);
        const match = arr.find(e=>{
          const est = (e.extendedProps?.estado || '').toUpperCase();
          const ei  = e.extendedProps?.hora_inicio || (e.start?.substring(11,16) || '');
          const ef  = e.extendedProps?.hora_fin    || (e.end  ?.substring(11,16) || '');
          return (est==='RESERVADO' || est==='CONFIRMADA') && ei===slot.hi && ef===slot.hf;
        });
        resolve(match ? match.id : null);
      }catch(_){ resolve(null); }
    }, 'json').fail(()=>resolve(null));
  });
}
</script>


<?php
else:
  $logout2 = new loginController();
  echo $logout2->login_session_force_destroy_controller();
endif;
