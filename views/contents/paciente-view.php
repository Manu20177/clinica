<?php if($_SESSION['userType']=="Secretaria"): ?>
<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-face zmdi-hc-fw"></i> Usuarios</h1>
	</div>
	<p class="lead">
		Bienvenido a la sección de usuarios, aquí podrás registrar nuevos usuarios (Los campos marcados con * son obligatorios para registrar un medico o una secretaria).
	</p>
</div>
<div class="container-fluid">
	<ul class="breadcrumb breadcrumb-tabs">
	  	<li class="active">
	  	<a href="<?php echo SERVERURL; ?>paciente/" class="btn btn-info">
	  		<i class="zmdi zmdi-plus"></i> Nuevo
	  	</a>
	  	</li>
	  	<li>
	  		<a href="<?php echo SERVERURL; ?>pacientelist/" class="btn btn-success">
	  			<i class="zmdi zmdi-format-list-bulleted"></i> Lista
	  		</a>
	  	</li>
	</ul>
</div>
<?php 
	require_once "./controllers/pacienteController.php";

	$inspaciente = new pacienteController();

	if(isset($_POST['nombres']) && isset($_POST['cedula'])){
		echo $inspaciente->add_paciente_controller();
	}
	$query2=$inspaciente->execute_single_query("SELECT * FROM sucursales");



?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
				    <h3 class="panel-title"><i class="zmdi zmdi-plus"></i> Nuevo Usuario</h3>
				</div>
			  	<div class="panel-body">
				    <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
						<fieldset>
							<legend><i class="zmdi zmdi-account-box"></i> Registro de Paciente</legend><br>
							<div class="container-fluid">
								<div class="row">

									<!-- Cédula -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Cédula *</label>
											<input pattern="[0-9]{10}" maxlength="10"
												oninput="this.value = this.value.replace(/[^0-9]/g, '')"
												class="form-control" type="text" name="cedula"
												value="<?php if(isset($_POST['cedula'])){ echo $_POST['cedula']; } ?>" 
												required>
										</div>
									</div>

									<!-- Nombres -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Nombres *</label>
											<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,100}" maxlength="100"
												class="form-control" type="text" name="nombres"
												value="<?php if(isset($_POST['nombres'])){ echo $_POST['nombres']; } ?>" 
												required>
										</div>
									</div>

									<!-- Apellidos -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Apellidos *</label>
											<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,100}" maxlength="100"
												class="form-control" type="text" name="apellidos"
												value="<?php if(isset($_POST['apellidos'])){ echo $_POST['apellidos']; } ?>" 
												required>
										</div>
									</div>

									<!-- Fecha de nacimiento -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating is-empty is-focused">
											<label class="control-label">Fecha de Nacimiento *</label>
											<input class="form-control" type="date" name="fecha_nacimiento"
												value="<?php if(isset($_POST['fecha_nacimiento'])){ echo $_POST['fecha_nacimiento']; } ?>" 
												required>
										</div>
									</div>

									<!-- Género -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating is-focused">
											<label class="control-label">Género *</label>
											<select class="form-control" name="genero" required>
												<option value="">Seleccione</option>
												<option value="Masculino">Masculino</option>
												<option value="Femenino">Femenino</option>
												<option value="Otro">Otro</option>
											</select>
										</div>
									</div>

									<!-- Estado civil -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating is-focused">
											<label class="control-label">Estado Civil</label>
											<select class="form-control" name="estado_civil">
												<option value="">Seleccione</option>
												<option value="Soltero">Soltero</option>
												<option value="Casado">Casado</option>
												<option value="Divorciado">Divorciado</option>
												<option value="Viudo">Viudo</option>
												<option value="Unión libre">Unión libre</option>
											</select>
										</div>
									</div>

									<!-- Teléfono -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Teléfono / Celular</label>
											<input pattern="[0-9]{7,10}" maxlength="10"
												oninput="this.value = this.value.replace(/[^0-9]/g, '')"
												class="form-control" type="text" name="telefono"
												value="<?php if(isset($_POST['telefono'])){ echo $_POST['telefono']; } ?>">
										</div>
									</div>

									<!-- Correo -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Correo electrónico</label>
											<input class="form-control" type="email" name="correo"
												value="<?php if(isset($_POST['correo'])){ echo $_POST['correo']; } ?>">
										</div>
									</div>

									<!-- Dirección -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Dirección</label>
											
											<input class="form-control" type="text" name="direccion"
												value="<?php if(isset($_POST['direccion'])){ echo $_POST['direccion']; } ?>">
										</div>
									</div>
									<!-- Estado civil -->
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating is-focused">
											<label class="control-label">Tipo de Sangre</label>
											<select class="form-control" name="tipo_sangre">
												<option value="">Seleccione</option>
												<option value="A+">A+</option>
												<option value="A-">A-</option>
												<option value="B+">B+</option>
												<option value="B-">B-</option>
												<option value="AB+">AB+</option>
												<option value="AB-">AB-</option>
												<option value="O+">O+</option>
												<option value="O-">O-</option>
											</select>
										</div>
									</div>

									
							

									<!-- Alergias -->
									<div class="col-xs-12">
										<div class="form-group label-floating">
											<label class="control-label">Alergias</label>
											<textarea class="form-control" name="alergias" rows="2"><?php if(isset($_POST['alergias'])){ echo $_POST['alergias']; } ?></textarea>
										</div>
									</div>

									<!-- Enfermedades -->
									<div class="col-xs-12">
										<div class="form-group label-floating">
											<label class="control-label">Enfermedades preexistentes</label>
											<textarea class="form-control" name="enfermedades" rows="2"><?php if(isset($_POST['enfermedades'])){ echo $_POST['enfermedades']; } ?></textarea>
										</div>
									</div>

									<!-- Sucursal -->
									

								</div>
							</div>
						</fieldset>


				    	
				    
					    <p class="text-center">
					    	<button type="submit" class="btn btn-info btn-raised btn-sm"><i class="zmdi zmdi-floppy"></i> Guardar</button>
					    </p>
				    </form>
			  	</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function () {
    // Cargar provincias
    $.ajax({
        url: '<?php echo SERVERURL."ajax/ajaxLocalidades.php"?>',
        type: 'POST',
        data: { action: 'load_provinces' },
        success: function(response) {
            $('#provincia').html(response);
        }
    });

    // Cargar cantones cuando cambia la provincia
    $('#provincia').change(function () {
        var id_provincia = $(this).val();
        if (id_provincia) {
            $.ajax({
                url: '<?php echo SERVERURL."ajax/ajaxLocalidades.php"?>',
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

    // Cargar parroquias cuando cambia el cantón
    $('#canton').change(function () {
        var id_canton = $(this).val();
        if (id_canton) {
            $.ajax({
                url: '<?php echo SERVERURL."ajax/ajaxLocalidades.php"?>',
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
<?php 
	else:
		$logout2 = new loginController();
        echo $logout2->login_session_force_destroy_controller(); 
	endif;
?>

<script>
function toggleSubmitButton(disabled) {
    $('#registroBtn').prop('disabled', disabled);
}

$('#pacientename').on('input', function () {
    const pacientename = $(this).val().trim();
    
    $('#pacientename-status').html(pacientename.length < 3 ? 'Escribe al menos 3 caracteres.' : '<i class="zmdi zmdi-refresh-alt zmdi-hc-spin"></i> Validando...');

    if (pacientename.length >= 3) {
        $.ajax({
            url: '<?php echo SERVERURL."ajax/ajaxValidarUsuario.php"?>',
            method: 'POST',
            data: { pacientename: pacientename },
            dataType: 'json',
            success: function (response) {
                const statusDiv = $('#pacientename-status');
                if (response.disponible === false) {
                    statusDiv.html('<span class="text-danger"><i class="zmdi zmdi-close-circle"></i> Usuario no disponible</span>');
                    toggleSubmitButton(true);
                } else if (response.disponible === true) {
                    statusDiv.html('<span class="text-success"><i class="zmdi zmdi-check-circle"></i> Usuario disponible</span>');
                    toggleSubmitButton(false);
                }
            },
            error: function () {
                $('#pacientename-status').html('<span class="text-warning">Error al validar.</span>');
                toggleSubmitButton(true);
            }
        });
    } else {
        $('#pacientename-status').html('<span class="text-warning">Escribe al menos 3 caracteres.</span>');
        toggleSubmitButton(true);
    }
});
</script>