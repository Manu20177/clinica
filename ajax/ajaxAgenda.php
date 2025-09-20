<?php
session_start();
date_default_timezone_set('America/Guayaquil');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

/* 🔧 MUY IMPORTANTE: obliga a controllers/models a usar rutas ../ */
$actionsRequired = true;

/* 🔧 LOG a archivo en vez de pantalla (evita romper JSON) */
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/agenda_ajax_error.log');

/* ✅ Permite SECRETARIA y también ADMINISTRADOR (útil para probar) */
$role = $_SESSION['userType'] ?? '';
if ($role !== 'Secretaria' && $role !== 'Administrador') {
  http_response_code(403);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(["ok"=>false, "error"=>"Acceso denegado (requiere Secretaría o Administrador)"]);
  exit;
}

require_once "../controllers/agendaController.php";
$agendaCtrl = new agendaController();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['action'])) {
  http_response_code(405);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(["ok"=>false, "error"=>"Método no permitido"]);
  exit;
}

$action = $_POST['action'];

/* ———— Handler seguro con catch para devolver JSON SIEMPRE ———— */
try {
  switch ($action) {

    /* === Diagnóstico rápido === */
    case 'diagnose':
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode([
        "ok"=>true,
        "session_userType"=>$role,
        "serverurl_sample"=>($_SERVER['HTTP_HOST'] ?? '').($_SERVER['REQUEST_URI'] ?? ''),
        "php_timezone"=>date_default_timezone_get(),
        "now"=>date('Y-m-d H:i:s')
      ]);
      break;

    /* === Pacientes legacy <li> === */
    case 'buscar_pacientes':
      header('Content-Type: text/html; charset=utf-8');
      $q = isset($_POST['q']) ? trim($_POST['q']) : '';
      echo $agendaCtrl->buscar_pacientes_controller($q);
      break;

    /* === Select2 (JSON) === */
    case 'buscar_pacientes_json':
      header('Content-Type: application/json; charset=utf-8');
      $q = isset($_POST['q']) ? trim($_POST['q']) : '';
      /* El controller ya devuelve JSON string */
      $json = $agendaCtrl->buscar_pacientes_json_controller($q);
      /* Garantiza que sea arreglo o [] */
      $arr = json_decode($json, true);
      if (!is_array($arr)) { $arr = []; }
      echo json_encode($arr, JSON_UNESCAPED_UNICODE);
      break;

    case 'load_especialidades':
      header('Content-Type: text/html; charset=utf-8');
      $sucursal_id = isset($_POST['sucursal_id']) ? trim($_POST['sucursal_id']) : null;
      echo $agendaCtrl->load_especialidades_controller($sucursal_id);
      break;

    case 'load_medicos':
        header('Content-Type: text/html; charset=utf-8');
        $especialidad_id = isset($_POST['especialidad_id']) ? intval($_POST['especialidad_id']) : 0;
        $sucursal_id     = isset($_POST['sucursal_id']) ? trim($_POST['sucursal_id']) : null;
        echo $agendaCtrl->load_medicos_controller($especialidad_id, $sucursal_id);
        break;


    case 'check_disponibilidad':
      header('Content-Type: application/json; charset=utf-8');
      $medico = isset($_POST['medico_codigo']) ? trim($_POST['medico_codigo']) : '';
      $fecha  = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
      $hi     = isset($_POST['hora_inicio']) ? trim($_POST['hora_inicio']) : '';
      $hf     = isset($_POST['hora_fin']) ? trim($_POST['hora_fin']) : '';
      echo $agendaCtrl->check_disponibilidad_controller($medico, $fecha, $hi, $hf);
      break;

    case 'listar_citas':
      header('Content-Type: application/json; charset=utf-8');
      $medico = isset($_POST['medico_codigo']) ? trim($_POST['medico_codigo']) : '';
      $start  = isset($_POST['start']) ? trim($_POST['start']) : '';
      $end    = isset($_POST['end']) ? trim($_POST['end']) : '';
      echo $agendaCtrl->listar_citas_controller($medico, $start, $end);
      break;

    default:
      http_response_code(400);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(["ok"=>false, "error"=>"Acción no reconocida"]);
      break;
  }
} catch (Throwable $e) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  error_log("[AJAX ERROR] ".$e->getMessage()." in ".$e->getFile().":".$e->getLine());
  echo json_encode(["ok"=>false, "error"=>"Excepción en servidor"]);
}
