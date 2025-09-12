<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-settings zmdi-hc-fw"></i> Datos del Usuario</h1>
	</div>
	<p class="lead">
		Bienvenido a la sección de actualización de los datos de las sucursales. Acá podrá actualizar la información de las sucursales registradas en el sistema.
	</p>
</div>
<?php 
	require_once "./controllers/sucursalController.php";

	$sucursalIns = new sucursalController();

	if(isset($_POST['code'])){
		echo $sucursalIns->update_sucursal_controller();
	}

	$code=explode("/", $_GET['views']);

	$data=$sucursalIns->data_sucursal_controller("Only",$code[1]);
	if($data->rowCount()>0):
		$rows=$data->fetch();
?>
<?php if($_SESSION['userType']=="Administrador"): ?>

<p class="text-center">
	<a href="<?php echo SERVERURL; ?>sucursallist/" class="btn btn-info btn-raised btn-sm">
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
				    		<input type="hidden" name="code" value="<?php echo $rows['id_suc']; ?>">
				    		<div class="container-fluid">
				    			<div class="row">
				    				<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Nombres *</label>
										  	<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" type="text" name="name" value="<?php if(isset($rows['nombre'])){ echo $rows['nombre']; } ?>" required="" maxlength="30">
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Direccion *</label>
										  	<input class="form-control" type="text" name="direccion" value="<?php if(isset($rows['direccion'])){ echo $rows['direccion']; } ?>" required="" maxlength="30">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Ciudad *</label>
										  	<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" type="text" name="ciudad" value="<?php if(isset($rows['ciudad'])){ echo $rows['ciudad']; } ?>" required="" maxlength="30">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Teléfono / Celular *</label>
											<input pattern="[0-9]{1,10}" class="form-control" type="text" 
												name="cell" 
												value="<?php if(isset($rows['telefono'])){ echo $rows['telefono']; } ?>" 
												required 
												maxlength="10">
										</div>
									</div>

									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Email *</label>
										  	<input class="form-control" type="email" name="correo" value="<?php if(isset($rows['email'])){ echo $rows['email']; } ?>" required="" maxlength="30">
										</div>
				    				</div>

									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Tipo*</label>

											<select class="form-control" name="tipo" required>
												<option value="">Seleccione el tipo...</option>
												<option value="1" <?php if(isset($rows['es_matriz']) && $rows['es_matriz']=="1"){ echo "selected"; } ?>>Matriz</option>
												<option value="0" <?php if(isset($rows['es_matriz']) && $rows['es_matriz']=="0"){ echo "selected"; } ?>>Sucursal</option>
											</select>
										</div>
									</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Estado *</label>

											<select class="form-control" name="estado" required>
												<option value="">Seleccione el estado...</option>
												<option value="Activo" <?php if(isset($rows['estado']) && $rows['estado']=="Activo"){ echo "selected"; } ?>>Activo</option>
												<option value="Inactivo" <?php if(isset($rows['estado']) && $rows['estado']=="Inactivo"){ echo "selected"; } ?>>Inactivo</option>
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