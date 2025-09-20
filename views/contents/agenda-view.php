<?php
if(isset($_SESSION['userType']) && $_SESSION['userType']==="Secretaria"):
  date_default_timezone_set('America/Guayaquil');
  require_once "./controllers/agendaController.php";
  require_once "./controllers/pacienteController.php";
  $agendaCtrl = new agendaController();
  $pc        = new pacienteController();

  // Guardar cita (POST directo)
  if(
    isset($_POST['paciente_id'], $_POST['sucursal_id'], $_POST['especialidad_id'],
          $_POST['medico_codigo'], $_POST['fecha'], $_POST['hora_inicio'], $_POST['hora_fin'])
  ){
      echo $agendaCtrl->add_cita_controller();
  }

  // Sucursal desde sesión (preseleccionada y bloqueada)
  $sucId = (int)($_SESSION['userIdSuc'] ?? 0);
  $sucStmt = $pc->execute_single_query("SELECT id_suc, nombre FROM sucursales WHERE id_suc={$sucId} LIMIT 1");
  $sucursalActual = $sucStmt && $sucStmt->rowCount()>0 ? $sucStmt->fetch(PDO::FETCH_ASSOC) : null;

  // Especialidades
  $espStmt = $pc->execute_single_query("SELECT id, nombre FROM especialidades WHERE estado='Activa' ORDER BY nombre ASC");
?>
<style>
.fc-toolbar-title { font-size: 1.6rem; }
.fc-timegrid-slot { height: 2.2em; }
.legend { margin-top: 10px; display:flex; gap:18px; align-items:center; flex-wrap:wrap; }
.legend .dot { width:14px; height:14px; display:inline-block; border-radius:3px; margin-right:6px; }
.dot-now { background:#5bc0de; }
.dot-reserved { background:#d9534f; }
.fc-event.reservado { background:#d9534f !important; border-color:#d9534f !important; color:#fff; }
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
      <div class="col-xs-12 col-sm-8">
        <div class="form-group label-floating is-focused">
          <label class="control-label">Paciente (cédula / nombres) *</label>
          <select id="paciente_id" name="paciente_id" class="form-control" style="width:100%;" required></select>
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
          <label class="control-label">Sucursal *</label>
          <select class="form-control" id="sucursal_id_view" disabled>
            <option value="">
              <?php echo $sucursalActual ? htmlspecialchars($sucursalActual['nombre'],ENT_QUOTES,'UTF-8') : 'SELECCIONE'; ?>
            </option>
          </select>
          <input type="hidden" name="sucursal_id" id="sucursal_id" value="<?php echo htmlspecialchars($sucId,ENT_QUOTES,'UTF-8'); ?>">
        </div>
      </div>

      <div class="col-xs-12 col-sm-4">
        <div class="form-group label-floating is-focused">
          <label class="control-label">Especialidad *</label>
          <select class="form-control" name="especialidad_id" id="especialidad_id" required>
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
          <select class="form-control" name="medico_codigo" id="medico_codigo" required disabled>
            <option value="">SELECCIONE</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Calendario -->
    <div class="row">
      <div class="col-xs-12">
        <div id="calendar"></div>
        <div class="legend">
          <span><span class="dot dot-now"></span>Fecha actual</span>
          <span><span class="dot dot-reserved"></span>Reservado</span>
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

<script>
(function(){
  const SERVER = '<?php echo SERVERURL; ?>';
  const $btn   = $('#btnGuardar');
  const $esp   = $('#especialidad_id');
  const $med   = $('#medico_codigo');

  const $fecha = $('#fecha');
  const $hi    = $('#hora_inicio');
  const $hf    = $('#hora_fin');

  let pacienteOk=false, medicoOk=false, slotOk=false;

  function toggleGuardar(){ $btn.prop('disabled', !(pacienteOk && medicoOk && slotOk)); }
  const pad2 = n => (n<10?('0'+n):(''+n));
  function toDateStr(d){ return d.getFullYear()+'-'+pad2(d.getMonth()+1)+'-'+pad2(d.getDate()); }
  function toTimeStr(d){ return pad2(d.getHours())+':'+pad2(d.getMinutes()); }

  // ---------- Select2 Paciente ----------
  const $pac = $('#paciente_id');
  $pac.select2({
    placeholder: 'Escriba cédula o nombre...',
    minimumInputLength: 2,
    ajax: {
      url: SERVER+'ajax/ajaxAgenda.php',
      type: 'POST',
      dataType: 'json',
      delay: 250,
      cache: false,
      data: params => ({ action: 'buscar_pacientes_json', q: params.term }),
      beforeSend: xhr => xhr.setRequestHeader('Cache-Control','no-store'),
      processResults: data => {
        // Select2 necesita [{id, text}, ...]
        return { results: Array.isArray(data) ? data : [] };
      },
      error: (xhr) => {
        const msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : xhr.statusText;
        console.error('Select2 error:', xhr.status, msg, xhr.responseText);
        swal("Error","No se pudo buscar pacientes ("+xhr.status+"): "+msg,"error");
      }
    },
    language: {
      inputTooShort: () => "Escriba al menos 2 caracteres...",
      noResults:     () => "Sin resultados",
      searching:     () => "Buscando..."
    }
  });

  $pac.on('change', function(){ pacienteOk = !!$(this).val(); toggleGuardar(); });

  // ---------- Cargar médicos por especialidad ----------
  $esp.on('change', function(){
    const espId = $(this).val();
    $med.prop('disabled', true).html('<option value="">Cargando...</option>');
    medicoOk=false; slotOk=false; toggleGuardar();
    $fecha.val(''); $hi.val(''); $hf.val('');
    if(!espId){
      $med.prop('disabled', true).html('<option value="">SELECCIONE</option>');
      if(calendar) calendar.refetchEvents();
      return;
    }
    $.post(SERVER+'ajax/ajaxAgenda.php', { action:'load_medicos', especialidad_id: espId }, function(html){
      $med.html(html).prop('disabled', false);
      if(calendar) calendar.refetchEvents();
    });
  });

  $med.on('change', function(){
    medicoOk = !!$(this).val();
    slotOk=false; toggleGuardar();
    $fecha.val(''); $hi.val(''); $hf.val('');
    if(calendar) calendar.refetchEvents();
  });

  // ---------- FullCalendar ----------
  let calendarEl = document.getElementById('calendar');
  let calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'es',
    height: 'auto',
    initialView: 'timeGridWeek',
    firstDay: 1,
    nowIndicator: true,
    headerToolbar: { left: 'today', center: 'title', right: 'prev,next' },

    businessHours: { daysOfWeek: [1,2,3,4,5], startTime: '09:00', endTime: '17:00' },
    hiddenDays: [0,6],
    validRange: { start: new Date() },

    slotMinTime: '09:00:00',
    slotMaxTime: '17:00:00',
    slotDuration: '00:30:00',
    snapDuration: '00:30:00',
    slotLabelInterval: { minutes: 30 },
    slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

    allDaySlot: false,
    selectable: true,
    selectOverlap: false,
    unselectAuto: true,
    selectMirror: true,

    selectAllow: function(sel){
      if(!$med.val()) return false;
      const now = new Date();
      if (sel.start < now) return false;
      // EXACTAMENTE 30 min
      const ms = sel.end.getTime() - sel.start.getTime();
      return ms === 30*60*1000;
    },

    select: function(info){
      const start = new Date(info.start);
      const end   = new Date(start.getTime() + 30*60*1000);

      $fecha.val(toDateStr(start));
      $hi.val(toTimeStr(start));
      $hf.val(toTimeStr(end));

      $.post(SERVER+'ajax/ajaxAgenda.php', {
        action: 'check_disponibilidad',
        medico_codigo: $med.val(),
        fecha: $fecha.val(),
        hora_inicio: $hi.val(),
        hora_fin: $hf.val()
      }, function(res){
        if(res && res.disponible){
          slotOk = true; toggleGuardar();
        }else{
          slotOk = false; toggleGuardar();
          swal("Conflicto de horario", "El médico ya tiene una cita en ese rango", "warning");
          $fecha.val(''); $hi.val(''); $hf.val('');
          calendar.unselect();
        }
      }, 'json').fail(function(){
        slotOk = false; toggleGuardar();
        swal("Error", "No se pudo validar disponibilidad", "error");
        $fecha.val(''); $hi.val(''); $hf.val('');
        calendar.unselect();
      });
    },

    events: function(fetchInfo, success, fail){
      const medico = $med.val();
      if(!medico){ success([]); return; }
      $.ajax({
        url: SERVER+'ajax/ajaxAgenda.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'listar_citas',
          medico_codigo: medico,
          start: fetchInfo.startStr,
          end: fetchInfo.endStr
        },
        success: function(res){
          const evs = (res||[]).map(e => ({ ...e, className:'reservado' }));
          success(evs);
        },
        error: fail
      });
    },
    eventOverlap: false
  });
  calendar.render();

  // ---------- Validación al enviar ----------
  $('#formCita').on('submit', function(e){
    if($btn.prop('disabled')){
      e.preventDefault();
      swal("Atención","Debe seleccionar paciente, médico y un horario disponible","warning");
      return;
    }
    $btn.prop('disabled', true).text('Guardando...');
  });
})();
</script>

<?php
else:
  $logout2 = new loginController();
  echo $logout2->login_session_force_destroy_controller();
endif;
