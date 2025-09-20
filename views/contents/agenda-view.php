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
.fc-toolbar-title { font-size: 1.6rem; }
.fc-timegrid-slot { height: 2.2em; }
.legend { margin-top: 10px; display:flex; gap:18px; align-items:center; flex-wrap:wrap; }
.legend .dot { width:14px; height:14px; display:inline-block; border-radius:3px; margin-right:6px; }
.dot-now { background:#5bc0de; }
.dot-reserved { background:#d9534f; }
.fc-event.reservado { background:#d9534f!important; border-color:#d9534f!important; color:#fff; }
.fc-timegrid-event.reservado .fc-event-main,
.fc-timegrid-event.reservado { background:#d9534f!important; border-color:#d9534f!important; color:#fff; }
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
  // —— URL ABSOLUTA del AJAX —— (evita errores de ruta)
 

(function(){
   const AJAX = '<?= rtrim(SERVERURL, "/"); ?>/ajax/ajaxAgenda.php';
  console.log('[AJAX URL]', AJAX);

  // Desactiva cache y loguea cualquier error global
  $.ajaxSetup({
    cache: false,
    error: function(xhr, status, err){
      console.error('[AJAX ERROR]', status, err, xhr.status, xhr.responseText);
    }
  });

  // —— PING DE DIAGNÓSTICO (debe salir SIEMPRE en Network) ——
  $.post(AJAX, { action:'diagnose' })
    .done(function(res){
      console.log('[diagnose OK]', res);
    })
    .fail(function(xhr){
      console.error('[diagnose FAIL]', xhr.status, xhr.responseText);
      alert('No se pudo conectar con ajaxAgenda.php ('+xhr.status+'). Revisa consola/Network.');
    });
  const SERVER = '<?php echo SERVERURL; ?>';
  const $btn   = $('#btnGuardar');
  const $esp   = $('#especialidad_id');
  const $med   = $('#id_especialidad_med');

  const $fecha = $('#fecha');
  const $hi    = $('#hora_inicio');
  const $hf    = $('#hora_fin');

  let pacienteOk=false, medicoOk=false, slotOk=false;
  function toggleGuardar(){ $btn.prop('disabled', !(pacienteOk && medicoOk && slotOk)); }

  // Select2 especialidad (solo estilo)
  $('.js-especialidad-select').select2({ placeholder:"Seleccione una especialidad", allowClear:true, width:'100%' });

  // Paciente (Select2)
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
      processResults: data => ({ results: Array.isArray(data) ? data : [] })
    },
    language: {
      inputTooShort: () => "Escriba al menos 2 caracteres...",
      noResults:     () => "Sin resultados",
      searching:     () => "Buscando..."
    }
  });
  $pac.on('change', function(){ pacienteOk = !!$(this).val(); toggleGuardar(); });

  // Cargar médicos (id_especialidad_med) por especialidad
  $esp.on('change', function(){
    const espId = $(this).val();
    $med.prop('disabled', true).html('<option value="">Cargando...</option>');
    medicoOk=false; slotOk=false; toggleGuardar();
    $fecha.val(''); $hi.val(''); $hf.val('');

    if(!espId){
      $med.prop('disabled', true).html('<option value="">SELECCIONE</option>');
      if (typeof calendar !== 'undefined') calendar.refetchEvents();
      return;
    }

    $.post(SERVER+'ajax/ajaxAgenda.php', {
      action:'load_medicos',
      especialidad_id: espId,
      sucursal_id: $('#sucursal_id').val()
    }, function(html){
      // Debe venir: <option value="id_especialidad_med">Dr(a). Nombre</option>
      $med.html(html).prop('disabled', false);
      if (typeof calendar !== 'undefined') calendar.refetchEvents();
    }).fail(function(xhr){
      console.error('load_medicos FAIL', xhr.status, xhr.responseText);
      $med.html('<option value="">Error cargando médicos</option>').prop('disabled', true);
    });
  });

  // Cambio de “médico”
  $med.on('change', function(){
    medicoOk = !!$(this).val();
    slotOk=false; toggleGuardar();
    $fecha.val(''); $hi.val(''); $hf.val('');
    if (typeof calendar !== 'undefined') calendar.refetchEvents();
  });

  // ---------- FullCalendar ----------
  const today00 = new Date(); today00.setHours(0,0,0,0);
  function hm(date){ const h = String(date.getHours()).padStart(2,'0'); const m = String(date.getMinutes()).padStart(2,'0'); return `${h}:${m}`; }
  function isWeekday(d){ const dow = d.getDay(); return dow >= 1 && dow <= 5; }
  function within9to17(start, end){ const hs = hm(start), he = hm(end); return hs >= '09:00' && he <= '17:00'; }

  let calendarEl = document.getElementById('calendar');
  let calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'es',
    height: 'auto',
    initialView: 'timeGridWeek',
    firstDay: 1,
    nowIndicator: true,
    timeZone: 'local',
    headerToolbar: { left: 'today', center: 'title', right: 'prev,next' },

    businessHours: { daysOfWeek: [1,2,3,4,5], startTime: '09:00', endTime: '17:00' },
    hiddenDays: [0,6],
    validRange: { start: today00 },

    slotMinTime: '09:00:00',
    slotMaxTime: '17:00:00',
    slotDuration: '00:30:00',
    snapDuration: '00:30:00',
    slotLabelInterval: { minutes: 30 },
    slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

    allDaySlot: false,
    selectable: true,
    unselectAuto: true,
    selectMirror: true,
    selectOverlap: false,

    // PINTADO FORZADO en cliente (por si el backend no manda "classNames")
    eventDidMount: function(info){
      if(!(info.event.classNames||[]).includes('reservado') &&
        info.event.title && info.event.title.toLowerCase().includes('reservado')){
        info.el.classList.add('reservado');
      }
    },

    // Reglas duras de selección
    selectAllow: function(sel){
      if(!$med.val()) return false;
      const ms = sel.end.getTime() - sel.start.getTime();
      if(ms !== 30*60*1000) return false;
      if(sel.start < today00) return false;
      if(!isWeekday(sel.start) || !isWeekday(sel.end)) return false;
      if(!within9to17(sel.start, sel.end)) return false;
      return true;
    },

    select: function(info){
      const start = new Date(info.start);
      const end   = new Date(info.end);

      $fecha.val(`${start.getFullYear()}-${String(start.getMonth()+1).padStart(2,'0')}-${String(start.getDate()).padStart(2,'0')}`);
      $hi.val(`${String(start.getHours()).padStart(2,'0')}:${String(start.getMinutes()).padStart(2,'0')}`);
      $hf.val(`${String(end.getHours()).padStart(2,'0')}:${String(end.getMinutes()).padStart(2,'0')}`);

      $.post(SERVER+'ajax/ajaxAgenda.php', {
        action: 'check_disponibilidad',
        id_especialidad_med: $med.val(),
        fecha: $fecha.val(),
        hora_inicio: $hi.val(),
        hora_fin: $hf.val()
      }, function(res){
        if(res && res.disponible){
          slotOk = true; toggleGuardar();
        }else{
          slotOk = false; toggleGuardar();
          swal("Conflicto de horario", "Ya existe una cita en ese rango", "warning");
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

    // Citas reservadas del médico/especialidad
    events: function(fetchInfo, success, fail){
      const idEM = $med.val();
      if(!idEM){ success([]); return; }

      $.ajax({
        url: SERVER+'ajax/ajaxAgenda.php',
        type: 'POST',
        dataType: 'json',
        cache: false,
        headers: { 'Cache-Control':'no-store' },
        data: {
          action: 'listar_citas',
          id_especialidad_med: idEM,
          start: fetchInfo.startStr,
          end: fetchInfo.endStr
        },
        success: function(res){
          try{
            const arr = (typeof res === 'string') ? JSON.parse(res) : res;
            const withClass = (Array.isArray(arr) ? arr : []).map(e => {
              // forzar clase si faltara
              if(!e.classNames){ e.classNames = []; }
              if(!e.classNames.includes('reservado')) e.classNames.push('reservado');
              return e;
            });
            success(withClass);
          }catch(e){
            console.error('JSON parse error events:', e, res);
            success([]);
          }
        },
        error: function(xhr){
          console.error('events FAIL', xhr.status, xhr.responseText);
          fail(xhr);
        }
      });
    },


    eventOverlap: false
  });

  calendar.render();

  // ---------- Guardado por AJAX + refetch ----------
  // Así ves el evento "Reservado" al instante, sin recargar toda la página
  $('#formCita').on('submit', function(e){
    if($btn.prop('disabled')){
      e.preventDefault();
      swal("Atención","Debe seleccionar paciente, médico y un horario disponible","warning");
      return;
    }
    e.preventDefault(); // <- evitamos recarga completa
    $btn.prop('disabled', true).text('Guardando...');

    $.post(window.location.href, $(this).serialize())
      .done(function(html){
        // Tu controller devuelve un sweet_alert en HTML.
        // Consideramos éxito si contiene "Cita registrada".
       if(/Cita registrada/i.test(html)){
          swal("OK","La cita se registró con éxito","success");

          // Pintado inmediato (opcional):
          const startT = $('#fecha').val() + 'T' + $('#hora_inicio').val() + ':00';
          const endT   = $('#fecha').val() + 'T' + $('#hora_fin').val() + ':00';
          calendar.addEvent({
            title: 'Reservado',
            start: startT,
            end: endT,
            classNames: ['reservado']  // <- consistente con el feed
          });

          // Y recarga el feed para quedar sincronizado con BD:
          calendar.refetchEvents();

          // Limpieza
          $('#hora_inicio,#hora_fin,#fecha').val('');
          slotOk=false; toggleGuardar();
          calendar.unselect();
        }else{
          // Muestra lo que devolvió (por si vino un error formateado)
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
})();
</script>


<?php
else:
  $logout2 = new loginController();
  echo $logout2->login_session_force_destroy_controller();
endif;
