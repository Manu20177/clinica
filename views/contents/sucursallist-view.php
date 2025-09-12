<?php if($_SESSION['userType']=="Administrador"): ?>
<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-account zmdi-hc-fw"></i> Sucursales </h1>
	</div>
	<p class="lead">
		En esta sección puede ver el listado de todas las Sucursales registradas en el sistema, puede actualizar datos o eliminar una sucursal cuando lo desee.
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
	if(isset($_POST['sucursalCode']) && isset($_POST['sucursalEstado'])){
		echo $inssucursal->toggle_estado_sucursal_controller($_POST['sucursalCode'], $_POST['sucursalEstado']);
	}

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
	  		<div class="panel panel-success">
			  	<div class="panel-heading">
			    	<h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de sucursalistradores</h3>
			  	</div>
			  	<div class="panel-body">
					<div class="table-responsive">
						<?php
							echo $inssucursal->pagination_sucursal_controller();
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