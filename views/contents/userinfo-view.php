<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-settings zmdi-hc-fw"></i> Datos del Usuario</h1>
	</div>
	<p class="lead">
		Bienvenido a la sección de actualización de los datos de los usuarios. Acá podrá actualizar la información personal de las secretarias o medicos registrados en el sistema.
	</p>
</div>
<?php 
	require_once "./controllers/userController.php";

	$userIns = new userController();

	if(isset($_POST['code'])){
		echo $userIns->update_user_controller();
	}

	$code=explode("/", $_GET['views']);

	$data=$userIns->data_user_controller("Only",$code[1]);
	if($data->rowCount()>0):
		$rows=$data->fetch();
?>
<?php if($_SESSION['userType']=="Administrador"): ?>

<p class="text-center">
	<a href="<?php echo SERVERURL; ?>userlist/" class="btn btn-info btn-raised btn-sm">
		<i class="zmdi zmdi-long-arrow-return"></i> Volver
	</a>
</p>
<?php endif; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-success">
				<div class="panel-heading">
				    <h3 class="panel-title"><i class="zmdi zmdi-refresh"></i> Actualizar datos</h3>
				</div>
			  	<div class="panel-body">
				    <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
				    	<fieldset>
				    		<legend><i class="zmdi zmdi-account-box"></i> Datos personales</legend><br>
				    		<input type="hidden" name="code" value="<?php echo $rows['Codigo']; ?>">
				    		<div class="container-fluid">
				    			<div class="row">
									<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Cédula *</label>
										  	<input pattern="[0-9]{1,10}" maxlength="10"   oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" type="text" name="cedula" value="<?php echo $rows['Cedula']; ?>" required="">
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Nombres *</label>
										  	<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" type="text" name="name" value="<?php echo $rows['Nombres']; ?>" required="" maxlength="30">
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Apellidos *</label>
										  	<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" type="text" name="lastname" value="<?php echo $rows['Apellidos']; ?>" required="" maxlength="30">
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Email</label>
										  	<input class="form-control" type="email" name="email" value="<?php echo $rows['Email']; ?>">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Telefono / Celular *</label>
										  	<input pattern="[0-9]{1,10}" maxlength="10"   oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" type="text" name="telefono" value="<?php echo $rows['Telefono']; ?>" required="">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Tipo de Usuario *</label>
											  <select class="form-control" name="tipousu">
												
												<option value="1" <?php if($rows['Tipo'] == '1') echo 'selected'; ?>>Socio</option>
												<option value="2" <?php if($rows['Tipo'] == '2') echo 'selected'; ?>>Representante</option>
												<option value="3" <?php if($rows['Tipo'] == '3') echo 'selected'; ?>>Trabajador</option>
												<option value="4" <?php if($rows['Tipo'] == '4') echo 'selected'; ?>>Directivo</option>
											  </select>
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Nivel de Estudios *</label>
											<select class="form-control" name="nivel" id="">
												<option value="Inicial" <?php if($rows['Nivel'] == 'Inicial') echo 'selected'; ?>>Educación Inicial</option>
												<option value="Primaria" <?php if($rows['Nivel'] == 'Primaria') echo 'selected'; ?>>Educación General Básica (Primaria)</option>
												<option value="Secundaria" <?php if($rows['Nivel'] == 'Secundaria') echo 'selected'; ?>>Bachillerato General Unificado (Secundaria)</option>
												<option value="TercerNivel" <?php if($rows['Nivel'] == 'TercerNivel') echo 'selected'; ?>>Educación Superior (Tercer Nivel)</option>
												<option value="Postgrado" <?php if($rows['Nivel'] == 'Postgrado') echo 'selected'; ?>>Postgrado</option>
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<select class="form-control" name="provincia" id="provincia">
												<option value="">Seleccione una provincia</option>
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<select class="form-control" name="canton" id="canton" disabled>
												<option value="">Seleccione una provincia primero</option>
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<select class="form-control" name="parroquia" id="parroquia" disabled>
												<option value="">Seleccione un cantón primero</option>
											</select>
										</div>
									</div>
									

									
				    			</div>
				    		</div>
				    	</fieldset>
					    <p class="text-center">
					    	<button type="submit" class="btn btn-success btn-raised btn-sm"><i class="zmdi zmdi-refresh"></i> Guardar cambios</button>
					    </p>
				    </form>
			  	</div>
			</div>
		</div>
	</div>
</div>
<?php
// Asegúrate de que estos valores existan
$selectedProvincia = isset($rows['Provincia']) ? $rows['Provincia'] : '';
$selectedCanton = isset($rows['Canton']) ? $rows['Canton'] : '';
$selectedParroquia = isset($rows['Parroquia']) ? $rows['Parroquia'] : '';
?>
<script>
$(document).ready(function () {
    var selectedProvincia = "<?php echo $selectedProvincia; ?>";
    var selectedCanton = "<?php echo $selectedCanton; ?>";
    var selectedParroquia = "<?php echo $selectedParroquia; ?>";

    // Cargar todas las provincias
    $.ajax({
        url: '<?php echo SERVERURL; ?>ajax/ajaxLocalidades.php',
        type: 'POST',
        data: { action: 'load_provinces' },
        success: function(response) {
            $('#provincia').html(response);
            if (selectedProvincia) {
                $('#provincia').val(selectedProvincia); // Seleccionar provincia guardada

                // Cargar cantones de la provincia seleccionada
                $.ajax({
                    url: '<?php echo SERVERURL; ?>ajax/ajaxLocalidades.php',
                    type: 'POST',
                    data: { action: 'load_cantons', id_provincia: selectedProvincia },
                    success: function(response) {
                        $('#canton').html(response).prop('disabled', false);
                        if (selectedCanton) {
                            $('#canton').val(selectedCanton); // Seleccionar cantón guardado

                            // Cargar parroquias del cantón seleccionado
                            $.ajax({
                                url: '<?php echo SERVERURL; ?>ajax/ajaxLocalidades.php',
                                type: 'POST',
                                data: { action: 'load_parishes', id_canton: selectedCanton },
                                success: function(response) {
                                    $('#parroquia').html(response).prop('disabled', false);
                                    if (selectedParroquia) {
                                        $('#parroquia').val(selectedParroquia); // Seleccionar parroquia guardada
                                    }
                                }
                            });
                        }
                    }
                });
            }
        }
    });

    // Cuando cambia provincia, carga los cantones
    $('#provincia').change(function () {
        var id_provincia = $(this).val();
        if (id_provincia) {
            $.ajax({
                url: '<?php echo SERVERURL; ?>ajax/ajaxLocalidades.php',
                type: 'POST',
                data: { action: 'load_cantons', id_provincia: id_provincia },
                success: function(response) {
                    $('#canton').html(response).prop('disabled', false);
                    $('#parroquia').html('<option value="">Seleccione un cantón</option>').prop('disabled', true);
                }
            });
        } else {
            $('#canton').html('<option value="">Seleccione una provincia</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un cantón</option>').prop('disabled', true);
        }
    });

    // Cuando cambia cantón, carga las parroquias
    $('#canton').change(function () {
        var id_canton = $(this).val();
        if (id_canton) {
            $.ajax({
                url: '<?php echo SERVERURL; ?>ajax/ajaxLocalidades.php',
                type: 'POST',
                data: { action: 'load_parishes', id_canton: id_canton },
                success: function(response) {
                    $('#parroquia').html(response).prop('disabled', false);
                }
            });
        } else {
            $('#parroquia').html('<option value="">Seleccione un cantón</option>').prop('disabled', true);
        }
    });
});
</script>
<?php else: ?>
	<p class="lead text-center">Lo sentimos ocurrió un error inesperado</p>
<?php
		endif;

?>