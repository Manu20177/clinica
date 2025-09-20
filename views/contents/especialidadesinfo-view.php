<?php if($_SESSION['userType']=="Administrador"): ?>
<div class="container-fluid">
  <div class="page-header">
    <h1 class="text-titles"><i class="zmdi zmdi-settings zmdi-hc-fw"></i> Datos de la Especialidad</h1>
  </div>
  <p class="lead">
    En esta sección puede actualizar la información de las <b>especialidades</b> registradas en el sistema.
  </p>
</div>

<?php 
  require_once "./controllers/especialidadesController.php";
  $espCtrl = new especialidadesController();

  /* Guardar cambios */
  if(isset($_POST['id_especialidad'])){
    echo $espCtrl->update_especialidad_controller();
  }

  /* Obtener ID desde la URL: /especialidadesinfo/<id> */
  $code = explode("/", $_GET['views']);
  $idEsp = isset($code[1]) ? (int)$code[1] : 0;

  $data = $espCtrl->data_especialidad_controller("Only", $idEsp);
  if($data && $data->rowCount()>0):
    $row = $data->fetch();
?>

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
    <li>
      <a href="<?php echo SERVERURL; ?>espemedicolist/" class="btn btn-success">
        <i class="zmdi zmdi-format-list-bulleted"></i> Médico - Especialidad
      </a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <div class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="zmdi zmdi-refresh"></i> Actualizar datos</h3>
        </div>
        <div class="panel-body">
          <form action="" method="POST" autocomplete="off">
            <fieldset>
              <legend><i class="zmdi zmdi-label"></i> Información de la especialidad</legend><br>
              <input type="hidden" name="id_especialidad" value="<?php echo (int)$row['id']; ?>">

              <div class="container-fluid">
                <div class="row">
                  <!-- Nombre -->
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                      <label class="control-label">Nombre de la especialidad *</label>
                      <input 
                        class="form-control" 
                        type="text" 
                        name="nombre" 
                        value="<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?>" 
                        required
                        maxlength="100"
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,-]{1,100}">
                    </div>
                  </div>

                  <!-- Estado -->
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                      <label class="control-label">Estado *</label>
                      <select class="form-control" name="estado" required>
                        <?php $estadoActual = $row['estado'] ?? 'Activa'; ?>
                        <option value="Activa"   <?php echo ($estadoActual=='Activa')?'selected':''; ?>>Activa</option>
                        <option value="Inactiva" <?php echo ($estadoActual=='Inactiva')?'selected':''; ?>>Inactiva</option>
                      </select>
                    </div>
                  </div>

                  <!-- (Opcional) Descripción breve -->
                  <!--
                  <div class="col-xs-12">
                    <div class="form-group label-floating">
                      <label class="control-label">Descripción (opcional)</label>
                      <textarea class="form-control" name="descripcion" rows="2" maxlength="255"><?php // echo htmlspecialchars($row['descripcion']??'', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                  </div>
                  -->

                </div>
              </div>
            </fieldset>

            <p class="text-center">
              <button type="submit" class="btn btn-success btn-raised btn-sm">
                <i class="zmdi zmdi-refresh"></i> Guardar cambios
              </button>
            </p>
          </form>

          <!-- Bloque de ayuda/alerta por integridad referencial -->
          <div class="alert alert-info" role="alert" style="margin-top:15px">
            <i class="zmdi zmdi-info-outline"></i>
            Recuerde que si una especialidad está asociada a médicos, 
            no podrá eliminarse hasta retirar esas asociaciones.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php else: ?>
  <p class="lead text-center">No se encontró la especialidad solicitada.</p>
  <p class="text-center">
    <a href="<?php echo SERVERURL; ?>especialidadeslist/" class="btn btn-default btn-sm">
      <i class="zmdi zmdi-format-list-bulleted"></i> Ir al listado
    </a>
  </p>
<?php endif; ?>

<?php 
  else:
    $logout2 = new loginController();
    echo $logout2->login_session_force_destroy_controller(); 
  endif;
?>
