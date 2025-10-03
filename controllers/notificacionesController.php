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
  public function pagination_notificaciones_controller(){
    $id_user = $_SESSION['userKey'];

    $code = explode("/", $_GET['views']);
    $estado = isset($code[1]) ? $code[1] : 'all';
    $whereEstado = "";
    if($estado === 'unread'){
        $whereEstado = "AND leido_en IS NULL";
    } elseif($estado === 'read'){
        $whereEstado = "AND leido_en IS NOT NULL";
    }

    $q = self::execute_single_query("SELECT * FROM `notificaciones` WHERE user_key='$id_user' $whereEstado ORDER BY creado_en DESC");
    $rows = $q ? $q->fetchAll() : [];

    // CSS embebido (una vez por render)
    $css = '
    <style>
      /* Badges estado */
      .badge-estado{
        display:inline-flex; align-items:center; gap:6px;
        padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600;
        line-height:1; white-space:nowrap;
      }
      .badge-unread{ background:#ffe8e8; color:#c82333; border:1px solid #f5c6cb; }
      .badge-read  { background:#e7f6ee; color:#0f6b3d; border:1px solid #b7e4c7; }

      .badge-estado i{ font-size:14px; line-height:1; }

      /* Puntito a la derecha del estado (opcional) */
      .dot{
        display:inline-block; width:8px; height:8px; border-radius:50%;
        background:currentColor;
      }

      /* Resaltar fila no leída */
      .row-unread{ background: #f8fbff; }
      .row-unread:hover{ background: #f1f7ff; }

      /* Celdas centradas donde aplica */
      #tabla-global td.text-center, #tabla-global th.text-center{ text-align:center; }

      /* Mensaje & título en una sola línea con elipsis si son largos (opcional) */
      .td-clip{
        max-width: 360px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
      }
      @media (max-width: 768px){
        .td-clip{ max-width: 200px; }
      }
    </style>';

    $table = $css . '
    <table id="tabla-global" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th class="text-center">Estado</th>
          <th>Título</th>
          <th>Mensaje</th>
          <th class="text-center">Creado en</th>
          <th class="text-center">Leído en</th>
        </tr>
      </thead>
      <tbody>
    ';

    $c = 1;
    foreach($rows as $r){
      $unread = (is_null($r['leido_en']) || $r['leido_en']==='');
      $estadoHtml = $unread
        ? '<span class="badge-estado badge-unread"><i class="zmdi zmdi-notifications-active"></i> No leído</span>'
        : '<span class="badge-estado badge-read"><i class="zmdi zmdi-check"></i> Leído</span>';

      $trClass = $unread ? ' class="row-unread"' : '';

      $titulo  = htmlspecialchars($r['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
      $mensaje = htmlspecialchars($r['mensaje'] ?? '', ENT_QUOTES, 'UTF-8');
      $creado  = htmlspecialchars($r['creado_en'] ?? '', ENT_QUOTES, 'UTF-8');
      $leido   = htmlspecialchars($r['leido_en'] ?? '—', ENT_QUOTES, 'UTF-8');

      $table .= '
        <tr'.$trClass.'>
          <td class="text-center">'.$c.'</td>
          <td class="text-center">'.$estadoHtml.'</td>
          <td class="td-clip" title="'. $titulo .'">'.$titulo.'</td>
          <td class="td-clip" title="'. $mensaje .'">'.$mensaje.'</td>
          <td class="text-center">'.$creado.'</td>
          <td class="text-center">'.$leido.'</td>
        </tr>';
      $c++;
    }

    $table .= '
      </tbody>
    </table>';

    return $table;
  }
  /* Acción: marcar todas como leídas */
  public function mark_all_read_controller(){
    $id_user = $_SESSION['userKey'] ?? '';
    if($id_user===''){
      return '<div class="alert alert-danger text-center">No autenticado</div>';
    }
    $this->mark_all_model($id_user);
    return '<div class="alert alert-success text-center">Todas las notificaciones fueron marcadas como leídas</div>';
  }

}
