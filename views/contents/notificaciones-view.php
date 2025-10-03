<div class="container-fluid">
  <div class="page-header">
    <h1 class="text-titles"><i class="zmdi zmdi-notifications zmdi-hc-fw"></i> Notificaciones</h1>
  </div>
  <p class="lead">
    En esta sección puede ver el listado de todas las notificaciones del sistema.
    Puede <b>marcar como leídas</b>, filtrar por <b>estado</b> y revisar el <b>detalle</b> de cada notificación.
  </p>
</div>

<div class="container-fluid">
  <ul class="breadcrumb breadcrumb-tabs">
    <li class="active">
      <a href="<?php echo SERVERURL; ?>notificaciones/" class="btn btn-success">
        <i class="zmdi zmdi-format-list-bulleted"></i> Ver todas
      </a>
    </li>
    <li>
      <a href="<?php echo SERVERURL; ?>notificaciones/unread" class="btn btn-info">
        <i class="zmdi zmdi-eye-off"></i> No leídas
      </a>
    </li>
    <li>
      <form action="" method="post" style="display:inline;">
        <input type="hidden" name="accion" value="marcar_todas">
        <button type="submit" class="btn btn-danger">
          <i class="zmdi zmdi-check-all"></i> Marcar todas como leídas
        </button>
      </form>
    </li>
  </ul>
</div>

<?php
  require_once "./controllers/notificacionesController.php";
  $notiCtrl = new notificacionesController();

  // Acciones simples (ej.: marcar todas)
  if(isset($_POST['accion']) && $_POST['accion']==='marcar_todas'){
    echo $notiCtrl->mark_all_read_controller();
  }
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <div class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de Notificaciones</h3>
        </div>
        <div class="panel-body">
          <div class="table-responsive">
            <?php
              // Puedes tomar filtros por GET si tu método los soporta (estado, texto, fechas, etc.)
              // Ejemplo: ?estado=unread / ?estado=read
              echo $notiCtrl->pagination_notificaciones_controller();
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
