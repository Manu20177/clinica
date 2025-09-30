<?php
session_start();
$actionsRequired = true;
require_once "../controllers/pagosController.php";
$ctrl = new pagosController();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action){
  

  case 'buscar_pacientes_json':
    $q = $_POST['q'] ?? '';
    header('Content-Type: application/json; charset=utf-8');
    echo $ctrl->buscar_pacientes_json_controller($q);
    break;

  /* NUEVO: citas por paciente (para Select2 dependiente) */
  case 'citas_por_paciente':
    $paciente_id = isset($_POST['paciente_id']) ? $_POST['paciente_id'] : 0;
    $q           = $_POST['q'] ?? ''; // texto libre opcional para filtrar
    header('Content-Type: application/json; charset=utf-8');
    echo $ctrl->citas_por_paciente_controller($paciente_id, $q);
    break;

  case 'anular_pago':
    $id = $_POST['id'] ?? '';
    $rz = $_POST['razon'] ?? '';
    $ctrl->anular_pago_controller($id, $rz);
    break;

  case 'crear_pago':
  header('Content-Type: application/json; charset=utf-8');
  echo $ctrl->crear_pago_controller(); // devuelve JSON
  break;

  default:
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>false,'error'=>'acción inválida']);
}
