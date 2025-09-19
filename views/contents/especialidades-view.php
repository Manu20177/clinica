<?php if($_SESSION['userType']=="Administrador"): ?>
<div class="container-fluid">
  <div class="page-header">
    <h1 class="text-titles"><i class="zmdi zmdi-label"></i> Especialidades</h1>
  </div>
  <p class="lead">
    Administra las especialidades. El estado permitido es <b>Activa</b> o <b>Inactiva</b>.
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
  require_once "./controllers/especialidadesController.php"; // si tu archivo se llama así
  $espCtrl = new especialidadesController();

  if(isset($_POST['nombre']) || isset($_POST['especialidad'])){
    // Normaliza para el controller: usa 'nombre' y 'estado'
    if(!isset($_POST['nombre']) && isset($_POST['especialidad'])){
      $_POST['nombre'] = $_POST['especialidad'];
    }
    if(!isset($_POST['estado'])){
      $_POST['estado'] = 'Activa';
    }
    echo $espCtrl->add_especialidad_controller();
  }
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="zmdi zmdi-plus"></i> Nueva Especialidad</h3>
        </div>
        <div class="panel-body">
          <form action="" method="POST" autocomplete="off">
            <fieldset>
              <legend><i class="zmdi zmdi-account-box"></i> Registro de Especialidades</legend><br>
              <div class="container-fluid">
                <div class="row">

                  <!-- Nombre de la especialidad -->
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                      <label class="control-label">Especialidad *</label>
                      <input 
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,100}" 
                        maxlength="100"
                        class="form-control" 
                        type="text" 
                        name="nombre" 
                        value="<?php 
                          // Mantiene compatibilidad por si venías usando 'especialidad'
                          if(isset($_POST['nombre'])){ 
                            echo $_POST['nombre']; 
                          }elseif(isset($_POST['especialidad'])){
                            echo $_POST['especialidad'];
                          }
                        ?>" 
                        required
                      >
                    </div>
                  </div>

                  <!-- Estado -->
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group label-floating">
                      <label class="control-label">Estado *</label>
                      <select class="form-control" name="estado" required>
                        <?php 
                          $estadoActual = $_POST['estado'] ?? 'Activa';
                        ?>
                        <option value="Activa"   <?php echo ($estadoActual=='Activa')?'selected':''; ?>>Activa</option>
                        <option value="Inactiva" <?php echo ($estadoActual=='Inactiva')?'selected':''; ?>>Inactiva</option>
                      </select>
                    </div>
                  </div>

                </div>
              </div>
            </fieldset>

            <p class="text-center">
              <button type="submit" class="btn btn-info btn-raised btn-sm">
                <i class="zmdi zmdi-floppy"></i> Guardar
              </button>
            </p>
          </form>
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
