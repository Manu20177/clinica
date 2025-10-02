<?php
// models/notificacionesModel.php
if ($actionsRequired) {
  require_once "../core/mainModel.php";
} else {
  require_once "./core/mainModel.php";
}

class notificacionesModel extends mainModel {

  /* Cuenta no leídas */
  protected function count_unread_model($userKey) {
    $sql = "SELECT COUNT(*) 
            FROM notificaciones
            WHERE user_key = :u AND leido_en IS NULL";
    $q = $this->connect()->prepare($sql);
    $q->bindParam(':u', $userKey);
    $q->execute();
    return (int)$q->fetchColumn();
  }

  /* Lista (prioriza no leídas), con límite */
  protected function list_model($userKey, $limit = 15) {
    $limit = max(1, min(50, (int)$limit));
    $sql = "SELECT id, titulo, mensaje, url, creado_en, leido_en
            FROM notificaciones
            WHERE user_key = :u
            ORDER BY (leido_en IS NULL) DESC, creado_en DESC
            LIMIT {$limit}";
    $q = $this->connect()->prepare($sql);
    $q->bindParam(':u', $userKey);
    $q->execute();
    return $q->fetchAll(PDO::FETCH_ASSOC);
  }

  /* Marcar todas como leídas */
  protected function mark_all_model($userKey) {
    $sql = "UPDATE notificaciones
            SET leido_en = NOW()
            WHERE user_key = :u AND leido_en IS NULL";
    $q = $this->connect()->prepare($sql);
    $q->bindParam(':u', $userKey);
    return $q->execute();
  }

  /* Marcar una como leída */
  protected function mark_read_model($userKey, $id) {
    $sql = "UPDATE notificaciones
            SET leido_en = NOW()
            WHERE user_key = :u AND id = :id";
    $q = $this->connect()->prepare($sql);
    $q->bindParam(':u',  $userKey);
    $q->bindParam(':id', $id, PDO::PARAM_INT);
    return $q->execute();
  }

  /* Crear notificación */
  protected function create_model($userKey, $title, $message, $url = '') {
    $sql = "INSERT INTO notificaciones (user_key, titulo, mensaje, url)
            VALUES (:u, :t, :m, :url)";
    $q = $this->connect()->prepare($sql);
    $q->bindParam(':u',   $userKey);
    $q->bindParam(':t',   $title);
    $q->bindParam(':m',   $message);
    $q->bindParam(':url', $url);
    $q->execute();
    return (int)$this->connect()->lastInsertId();
  }
}
