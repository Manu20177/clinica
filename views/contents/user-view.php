<?php if($_SESSION['userType']=="Administrador"): ?>
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
	  	<a href="<?php echo SERVERURL; ?>user/" class="btn btn-info">
	  		<i class="zmdi zmdi-plus"></i> Nuevo
	  	</a>
	  	</li>
	  	<li>
	  		<a href="<?php echo SERVERURL; ?>userlist/" class="btn btn-success">
	  			<i class="zmdi zmdi-format-list-bulleted"></i> Lista
	  		</a>
	  	</li>
	</ul>
</div>
<?php 
	require_once "./controllers/userController.php";

	$insuser = new userController();

	if(isset($_POST['name']) && isset($_POST['username'])){
		echo $insuser->add_user_controller();
	}
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
				    		<legend><i class="zmdi zmdi-account-box"></i> Datos personales</legend><br>
				    		<div class="container-fluid">
				    			<div class="row">
				    				<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Cédula *</label>
										  	<input pattern="[0-9]{1,10}" maxlength="10"   oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" type="text" name="cedula" value="<?php if(isset($_POST['cedula'])){ echo $_POST['cedula']; } ?>" required="">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Nombres *</label>
										  	<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" type="text" name="name" value="<?php if(isset($_POST['name'])){ echo $_POST['name']; } ?>" required="" maxlength="30">
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Apellidos *</label>
										  	<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" type="text" name="lastname" value="<?php if(isset($_POST['lastname'])){ echo $_POST['lastname']; } ?>" required="" maxlength="30">
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Email</label>
										  	<input class="form-control" type="email" name="email" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } ?>">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Telefono / Celular *</label>
										  	<input pattern="[0-9]{1,10}" maxlength="10"   oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" type="text" name="telefono" value="<?php if(isset($_POST['telefono'])){ echo $_POST['telefono']; } ?>" required="">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Tipo de Usuario *</label>
											<select class="form-control" name="tipousu" id="" >
												<option value="2">Medico</option>
												<option value="3">Secretaria</option>
											</select>
										</div>
				    				</div>

									<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Nivel de Estudios *</label>
											<select class="form-control" name="nivel" id="" >
												<option value="Inicial">Educación Inicial</option>
												<option value="Primaria">Educación General Básica (Primaria)</option>
												<option value="Secundaria">Bachillerato General Unificado (Secundaria)</option>
												<option value="TercerNivel">Educación Superior (Tercer Nivel)</option>
												<option value="Postgrado">Postgrado</option>
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
				    	<br><br>
				    	<fieldset>
				    		<legend><i class="zmdi zmdi-key"></i> Datos de la cuenta</legend><br>
							<div class="container-fluid">
								<div class="row">
								    <div id="username-status" style="margin-top: 5px;"></div>
									<div class="col-xs-12 col-sm-6">
                                       <div class="form-group label-floating">
                                           
    										<label class="control-label">Nombre de usuario *</label>
    										<input id="username" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ]{1,15}" 
    											class="form-control" type="text" name="username" id="username"
    											required maxlength="15">
    										<!-- Mensaje de disponibilidad -->
    									
    									</div>
    										
                                    </div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Genero</label>
										  	<select name="gender" class="form-control">
										  		<?php 
										  			if(isset($_POST['gender'])){ 
										  				echo '<option value="'.$_POST['gender'].'">'.$_POST['gender'].' Actual</option>'; 
										  			} 
										  		?>
									          	<option value="Masculino">Masculino</option>
									          	<option value="Femenino">Femenino</option>
									        </select>
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Contraseña *</label>
										  	<input class="form-control" type="password" name="password1" value="<?php if(isset($_POST['password1'])){ echo $_POST['password1']; } ?>" required="" maxlength="70">
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Repita la contraseña *</label>
										  	<input class="form-control" type="password" name="password2" value="<?php if(isset($_POST['password2'])){ echo $_POST['password2']; } ?>" required="" maxlength="70">
										</div>
				    				</div>
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

$('#username').on('input', function () {
    const username = $(this).val().trim();
    
    $('#username-status').html(username.length < 3 ? 'Escribe al menos 3 caracteres.' : '<i class="zmdi zmdi-refresh-alt zmdi-hc-spin"></i> Validando...');

    if (username.length >= 3) {
        $.ajax({
            url: '<?php echo SERVERURL."ajax/ajaxValidarUsuario.php"?>',
            method: 'POST',
            data: { username: username },
            dataType: 'json',
            success: function (response) {
                const statusDiv = $('#username-status');
                if (response.disponible === false) {
                    statusDiv.html('<span class="text-danger"><i class="zmdi zmdi-close-circle"></i> Usuario no disponible</span>');
                    toggleSubmitButton(true);
                } else if (response.disponible === true) {
                    statusDiv.html('<span class="text-success"><i class="zmdi zmdi-check-circle"></i> Usuario disponible</span>');
                    toggleSubmitButton(false);
                }
            },
            error: function () {
                $('#username-status').html('<span class="text-warning">Error al validar.</span>');
                toggleSubmitButton(true);
            }
        });
    } else {
        $('#username-status').html('<span class="text-warning">Escribe al menos 3 caracteres.</span>');
        toggleSubmitButton(true);
    }
});
</script>