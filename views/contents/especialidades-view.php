<?php if($_SESSION['userType']=="Administrador"): ?>
<?php 
  /* ================== CARGA CONTROLLER ================== */
  require_once "./controllers/especialidadesController.php"; 
  require_once "./core/mainModel.php"; 

  $espCtrl = new especialidadesController();

  /* ================== ACCIONES POST ================== */

  // 1) Crear especialidad
  if(isset($_POST['nombre']) && isset($_POST['estado'])){
    echo $espCtrl->add_especialidad_controller();
  }

  // 2) Crear relación médico ↔ especialidad
  if(isset($_POST['medico_codigo']) && isset($_POST['especialidad_id'])){
    echo $espCtrl->add_medico_especialidad_controller();
  }

  /* ================== DATOS PARA SELECT ================== */
  try{
    $stmtEsp = $espCtrl->execute_single_query("SELECT id,nombre FROM especialidades WHERE estado='Activa' ORDER BY nombre ASC");
    $especialidadesActivas = $stmtEsp ? $stmtEsp->fetchAll(PDO::FETCH_ASSOC) : [];
  }catch(Exception $e){
    $especialidadesActivas = [];
  }

  /* ================== DATOS PARA SELECT ================== */
  try{
    $stmtMed = $espCtrl->execute_single_query("
      SELECT * 
      FROM `cuenta` c 
      LEFT JOIN usuarios u ON u.Codigo = c.Codigo 
      WHERE c.Privilegio = 3 AND u.Estado = 'Activo';
    ");
    $MedicosA = $stmtMed ? $stmtMed->fetchAll(PDO::FETCH_ASSOC) : [];
  }catch(Exception $e){
    $MedicosA = [];
  }
?>

<div class="container-fluid">
  <div class="page-header">
    <h1 class="text-titles"><i class="zmdi zmdi-label"></i> Gestión de Especialidades</h1>
  </div>
  <p class="lead">
    Administra las especialidades y asigna médicos a ellas. 
    El estado permitido es <b>Activa</b> o <b>Inactiva</b>.
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
    <li>
      <a href="<?php echo SERVERURL; ?>espemedicolist/" class="btn btn-success">
        <i class="zmdi zmdi-format-list-bulleted"></i> Médico - Especialidad
      </a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <div class="row">

    <!-- === Panel: Nueva Especialidad === -->
    <div class="col-xs-12 col-md-6">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="zmdi zmdi-plus"></i> Nueva Especialidad</h3>
        </div>
        <div class="panel-body">
          <form action="" method="POST" autocomplete="off">
            <fieldset>
              <legend><i class="zmdi zmdi-label"></i> Registro</legend>

              <div class="form-group label-floating">
                <label class="control-label">Nombre de la especialidad *</label>
                <input 
                  pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,100}" 
                  maxlength="100"
                  class="form-control" 
                  type="text" 
                  name="nombre" 
                  required>
              </div>

              <div class="form-group">
                <label>Estado *</label>
                <select class="form-control" name="estado" required style="width:100%">
                  <option value="Activa">Activa</option>
                  <option value="Inactiva">Inactiva</option>
                </select>
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

    <!-- === Panel: Relacionar Médico ↔ Especialidad === -->
    <div class="col-xs-12 col-md-6">
      <div class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="zmdi zmdi-accounts"></i> Relacionar Médico con Especialidad</h3>
        </div>
        <div class="panel-body">
          <form action="" method="POST" autocomplete="off">
            <fieldset>
              <legend><i class="zmdi zmdi-link"></i> Nueva Relación</legend>

              <!-- Código del médico con búsqueda -->
              <div class="form-group">
                <label >Médico *</label>
                <select 
                  class="form-control js-medico-select" 
                  name="medico_codigo" 
                  required
                  style="width:100%">
                  <option value="">Seleccione un médico</option>
                  <?php if(!empty($MedicosA)): ?>
                    <?php foreach($MedicosA as $med): ?>
                      <option value="<?php echo $med['Codigo']; ?>">
                        <?php 
                          $ap = isset($med['Apellidos']) ? $med['Apellidos'] : '';
                          $no = isset($med['Nombres']) ? $med['Nombres'] : '';
                          echo htmlspecialchars(trim($ap.' '.$no).' ('.$med['Cedula'].')'); 
                        ?>
                      </option>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <option value="" disabled>No hay médicos registrados</option>
                  <?php endif; ?>
                </select>
              </div>

              <!-- Especialidad -->
              <div class="form-group">
                <label class="">Especialidad *</label>
                <select class="form-control js-especialidad-select" name="especialidad_id" required style="width:100%">
                  <option value="">Seleccione una especialidad</option>
                  <?php if(!empty($especialidadesActivas)): ?>
                    <?php foreach($especialidadesActivas as $esp): ?>
                      <option value="<?php echo $esp['id']; ?>">
                        <?php echo htmlspecialchars($esp['nombre']); ?>
                      </option>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <option value="" disabled>No hay especialidades activas</option>
                  <?php endif; ?>
                </select>
              </div>

            </fieldset>

            <p class="text-center">
              <button type="submit" class="btn btn-success btn-raised btn-sm">
                <i class="zmdi zmdi-link"></i> Relacionar
              </button>
            </p>
          </form>
        </div>
      </div>
    </div>

    <!-- Parche CSS por si algún style oculta la barra de búsqueda -->
    <style>
      .select2-container .select2-search--dropdown { display: block !important; }
      .select2-container .select2-search__field { display: block !important; }
    </style>

    <!-- Inicialización de Select2 (buscador forzado) -->
    <script>
      $(function () {
        // Médico (con buscador)
        $('.js-medico-select').select2({
          placeholder: "Seleccione un médico",
          allowClear: true,
          minimumResultsForSearch: 0,
          width: '100%'
        });

        // Especialidad (con buscador)
        $('.js-especialidad-select').select2({
          placeholder: "Seleccione una especialidad",
          allowClear: true,
          minimumResultsForSearch: 0,
          width: '100%'
        });

      

        // Debug rápido
        console.log('Select2 disponible:', typeof $.fn.select2);
      });
    </script>

  </div>
</div>

<?php 
  else:
    $logout2 = new loginController();
    echo $logout2->login_session_force_destroy_controller(); 
  endif;
?>
