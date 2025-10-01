<?php
// controllers/notificacionesController.php
if ($actionsRequired) {
  require_once "../models/notificacionesModel.php";
} else {
  require_once "./models/notificacionesModel.php";
}

class notificacionesController extends notificacionesModel {

  /* Contar no leídas (por si lo necesitas en algún sitio) */
  public function count_controller() {
    $userKey = $_SESSION['userKey'] ?? '';
    if ($userKey==='') return json_encode(['ok'=>false,'error'=>'No autenticado1']);
    $count = $this->count_unread_model($userKey);
    return json_encode(['ok'=>true, 'count'=>(int)$count]);
  }

  /* Listar (10 por defecto), devuelve r.data que tu JS espera */
  public function listar_controller($limit = 10) {
    $userKey = $_SESSION['userKey'] ?? '';
    if ($userKey==='') return json_encode(['ok'=>false,'error'=>"No autenticado2'"]);

    $rows = $this->list_model($userKey, $limit);

    // El JS espera r.data con campos: id, titulo, mensaje, url, creado_en, leido_en
    return json_encode(['ok'=>true, 'data'=>$rows]);
  }

  /* Marcar TODAS como leídas */
  public function marcar_todas_controller() {
    $userKey = $_SESSION['userKey'] ?? '';
    if ($userKey==='') return json_encode(['ok'=>false,'error'=>'No autenticado3']);
    $this->mark_all_model($userKey);
    return json_encode(['ok'=>true]);
  }

  /* Marcar UNA como leída */
  public function marcar_leida_controller($id) {
    $userKey = $_SESSION['userKey'] ?? '';
    if ($userKey==='') return json_encode(['ok'=>false,'error'=>'No autenticado4']);

    $id = (int)$id;
    if ($id<=0) return json_encode(['ok'=>false,'error'=>'ID inválido']);

    $this->mark_read_model($userKey, $id);
    return json_encode(['ok'=>true]);
  }

  /* Crear notificación (opcional) */
  public function crear_controller($title, $message, $url = '') {
    $userKey = $_SESSION['userKey'] ?? '';
    if ($userKey==='') return json_encode(['ok'=>false,'error'=>'No autenticado5']);
    $id = $this->create_model($userKey, $title, $message, $url);
    return json_encode(['ok'=>true,'id'=>$id]);
  }
}
