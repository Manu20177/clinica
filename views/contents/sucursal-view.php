<?php if($_SESSION['userType']=="Administrador"): ?>
<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-account zmdi-hc-fw"></i> Sucursales</h1>
	</div>
	<p class="lead">
		Bienvenido a la sección de sucursales, aquí podrás registrar nuevas sucursales (Los campos marcados con * son obligatorios para registrar una sucursal).
	</p>
</div>
<div class="container-fluid">
	<ul class="breadcrumb breadcrumb-tabs">
	  	<li class="active">
	  	<a href="<?php echo SERVERURL; ?>sucursal/" class="btn btn-info">
	  		<i class="zmdi zmdi-plus"></i> Nuevo
	  	</a>
	  	</li>
	  	<li>
	  		<a href="<?php echo SERVERURL; ?>sucursallist/" class="btn btn-success">
	  			<i class="zmdi zmdi-format-list-bulleted"></i> Lista
	  		</a>
	  	</li>
	</ul>
</div>
<?php 
	require_once "./controllers/sucursalController.php";

	$inssucursal = new sucursalController();

	if(isset($_POST['name']) && isset($_POST['tipo'])){
		echo $inssucursal->add_sucursal_controller();
	}
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
				    <h3 class="panel-title"><i class="zmdi zmdi-plus"></i> Nueva Sucursal</h3>
				</div>
			  	<div class="panel-body">
				    <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
				    	<fieldset>
				    		<div class="container-fluid">
				    			<div class="row">
				    				<div class="col-xs-12 col-sm-6">
								    	<div class="form-group label-floating">
										  	<label class="control-label">Nombres *</label>
										  	<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" type="text" name="name" value="<?php if(isset($_POST['name'])){ echo $_POST['name']; } ?>" required="" maxlength="30">
										</div>
				    				</div>
				    				<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Direccion *</label>
										  	<input class="form-control" type="text" name="direccion" value="<?php if(isset($_POST['direccion'])){ echo $_POST['direccion']; } ?>" required="" maxlength="30">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Ciudad *</label>
										  	<input pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" type="text" name="ciudad" value="<?php if(isset($_POST['ciudad'])){ echo $_POST['ciudad']; } ?>" required="" maxlength="30">
										</div>
				    				</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
											<label class="control-label">Teléfono / Celular *</label>
											<input pattern="[0-9]{1,10}" class="form-control" type="text" 
												name="cell" 
												value="<?php if(isset($_POST['cell'])){ echo $_POST['cell']; } ?>" 
												required 
												maxlength="10">
										</div>
									</div>

									<div class="col-xs-12 col-sm-6">
										<div class="form-group label-floating">
										  	<label class="control-label">Email *</label>
										  	<input class="form-control" type="email" name="correo" value="<?php if(isset($_POST['correo'])){ echo $_POST['correo']; } ?>" required="" maxlength="30">
										</div>
				    				</div>

									<div class="col-xs-12 col-sm-6">
										<div class="form-group">
											<select class="form-control" name="tipo" required>
												<option value="">Seleccione el tipo...</option>
												<option value="1" <?php if(isset($_POST['tipo']) && $_POST['tipo']=="1"){ echo "selected"; } ?>>Matriz</option>
												<option value="0" <?php if(isset($_POST['tipo']) && $_POST['tipo']=="0"){ echo "selected"; } ?>>Sucursal</option>
											</select>
										</div>
									</div>

				    			</div>
				    		</div>
				    	</fieldset>
				    	<br><br>
				
					    <p class="text-center">
					    	<button type="submit" class="btn btn-info btn-raised btn-sm"><i class="zmdi zmdi-floppy"></i> Guardar</button>
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
