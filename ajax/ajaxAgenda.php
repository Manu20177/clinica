<?php
session_start(); // Importante si usas $_SESSION
$actionsRequired = true;

if(!isset($_SESSION['userType']) || $_SESSION['userType']!=='Secretaria'){
    http_response_code(403);
    echo '<script>swal("Acceso denegado","Solo Secretaría","error");</script>';
    exit;
}

require_once "../controllers/agendaController.php";
$agendaCtrl = new agendaController();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {

    switch ($_POST['action']) {

        case 'buscar_pacientes':
            // Parámetro: q
            $q = isset($_POST['q']) ? trim($_POST['q']) : '';
            echo $agendaCtrl->buscar_pacientes_controller($q);
            break;

        case 'load_especialidades':
            // (Opcional) sucursal_id si luego filtras por sucursal
            $sucursal_id = isset($_POST['sucursal_id']) ? trim($_POST['sucursal_id']) : null;
            echo $agendaCtrl->load_especialidades_controller($sucursal_id);
            break;

        case 'load_medicos':
            // Requiere: especialidad_id
            $especialidad_id = isset($_POST['especialidad_id']) ? intval($_POST['especialidad_id']) : 0;
            $sucursal_id     = isset($_POST['sucursal_id']) ? trim($_POST['sucursal_id']) : null;
            echo $agendaCtrl->load_medicos_controller($especialidad_id, $sucursal_id);
            break;

        case 'check_disponibilidad':
            // Requiere: medico_codigo, fecha (YYYY-mm-dd), hora_inicio (HH:ii), hora_fin (HH:ii)
            header('Content-Type: application/json; charset=utf-8');
            $medico = isset($_POST['medico_codigo']) ? trim($_POST['medico_codigo']) : '';
            $fecha  = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
            $hi     = isset($_POST['hora_inicio']) ? trim($_POST['hora_inicio']) : '';
            $hf     = isset($_POST['hora_fin']) ? trim($_POST['hora_fin']) : '';
            echo $agendaCtrl->check_disponibilidad_controller($medico, $fecha, $hi, $hf);
            break;

        default:
            http_response_code(400);
            echo '<script>swal("Error","Acción no reconocida","error");</script>';
            break;
    }

} else {
    http_response_code(405);
    echo '<script>swal("Error", "Método no permitido", "error");</script>';
}
