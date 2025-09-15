<?php if($_SESSION['userType']=="Secretaria"): ?>
<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-face zmdi-hc-fw"></i> Pacientes</h1>
	</div>
	<p class="lead">
		En esta sección puede ver el listado de todos los pacientes registrados en el sistema, puede actualizar datos o eliminar a un paciente cuando lo desee.
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

	if(isset($_POST['pacienteCode'])){
		echo $inspaciente->delete_paciente_controller($_POST['pacienteCode']);
	}
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
	  		<div class="panel panel-success">
			  	<div class="panel-heading">
			    	<h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de Usuarios</h3>
			  	</div>
			  	<div class="panel-body">
					<div class="table-responsive">
						<?php
							echo $inspaciente->pagination_paciente_controller();
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