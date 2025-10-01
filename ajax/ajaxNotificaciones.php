<?php
session_start();
$actionsRequired = true;
require_once "../controllers/notificacionesController.php";

$ctrl = new notificacionesController();

$action = $_POST['action'] ?? $_GET['action'] ?? '';


switch ($action) {

  case 'listar':
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    header('Content-Type: application/json; charset=utf-8');
    echo $ctrl->listar_controller($limit);
    break;

  case 'marcar_todas':
    header('Content-Type: application/json; charset=utf-8');
    echo $ctrl->marcar_todas_controller();
    break;

  case 'marcar_leida':
    $id = $_POST['id'] ?? '';
    header('Content-Type: application/json; charset=utf-8');
    echo $ctrl->marcar_leida_controller($id);
    break;

  // Opcional: crear
  case 'crear':
    $t = $_POST['titulo']  ?? '';
    $m = $_POST['mensaje'] ?? '';
    $u = $_POST['url']     ?? '';
    header('Content-Type: application/json; charset=utf-8');
    echo $ctrl->crear_controller($t,$m,$u);
    break;

  default:
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>false, 'error'=>'Acción inválida']);
}
