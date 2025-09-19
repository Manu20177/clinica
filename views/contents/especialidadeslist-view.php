<?php if($_SESSION['userType']=="Administrador"): ?>
<div class="container-fluid">
  <div class="page-header">
    <h1 class="text-titles"><i class="zmdi zmdi-label zmdi-hc-fw"></i> Especialidades</h1>
  </div>
  <p class="lead">
    En esta sección puede ver el listado de todas las especialidades registradas en el sistema.
    Puede <b>crear</b> nuevas, <b>actualizar</b> datos o <b>eliminar</b> una especialidad cuando lo desee.
  </p>
</div>

<div class="container-fluid">
  <ul class="breadcrumb breadcrumb-tabs">
    <li class="active">
      <a href="<?php echo SERVERURL; ?>especialidades/" class="btn btn-info">
        <i class="zmdi zmdi-plus"></i> Nuevo
      </a>
    </li>
    <li>
      <a href="<?php echo SERVERURL; ?>especialidadeslist/" class="btn btn-success">
        <i class="zmdi zmdi-format-list-bulleted"></i> Lista
      </a>
    </li>
  </ul>
</div>

<?php
  require_once "./controllers/especialidadesController.php";
  $espCtrl = new especialidadesController();

  // Eliminar especialidad (desde botón/badge de la tabla)
  if(isset($_POST['especialidadId'])){
    echo $espCtrl->delete_especialidad_controller($_POST['especialidadId']);
  }
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <div class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de Especialidades</h3>
        </div>
        <div class="panel-body">
          <div class="table-responsive">
            <?php
              // Renderizado con paginación y búsqueda si tu controller lo soporta
              // Asegúrate de tener este método en el controller:
              //   public function pagination_especialidades_controller() { ... }
              echo $espCtrl->pagination_especialidades_controller();
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php 
  else:
    $logout2 = new loginController();
    echo $logout2->login_session_force_destroy_controller(); 
  endif;
?>
