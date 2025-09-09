<?php if($_SESSION['userType'] == "Administrador"): ?>
<div class="container-fluid">
	<div class="page-header">
	  <h1 class="text-titles"><i class="zmdi zmdi-time-restore"></i> Respaldo de la Base de Datos</h1>
	</div>
	<p class="lead text-center">
		Desde aquí puedes generar un respaldo completo de la base de datos del sistema. El respaldo se descargará como un archivo <strong>.ZIP</strong> automáticamente.
	</p>
</div>

<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 text-center">
			<button id="btn-backup" class="btn btn-primary btn-lg">
				<i class="zmdi zmdi-cloud-download"></i> Realizar respaldo
			</button>
			<br><br>
			<div id="progress-container" style="display: none;">
				<p><i class="zmdi zmdi-refresh zmdi-hc-spin"></i> Generando respaldo, por favor espera...</p>
				<div class="progress">
					<div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%;">
						Procesando...
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br><br>
<h3 class="text-center">📦 Respaldos realizados</h3>
<table id="tabla-global" class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th>Nombre del archivo</th>
			<th>Fecha</th>
			<th>Descargar</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$backupPath = "./core/respaldo"; // Ruta relativa desde el view
		$archivos = glob($backupPath . "/*.zip");
		if ($archivos && count($archivos) > 0) {
			$contador = 1;
			// Ordenar por fecha de creación descendente
			usort($archivos, function($a, $b) {
				return filemtime($b) - filemtime($a);
			});
			foreach ($archivos as $archivo) {
				$nombreArchivo = basename($archivo);
				$fecha = date("Y-m-d H:i:s", filemtime($archivo));
				echo "<tr>
						<td>$contador</td>
						<td>$nombreArchivo</td>
						<td>$fecha</td>
						<td><a href='$backupPath/$nombreArchivo' class='btn btn-success btn-sm' download><i class='zmdi zmdi-download'></i> Descargar</a></td>
					  </tr>";
				$contador++;
			}
		} else {
			echo "<tr><td colspan='4'>No hay respaldos disponibles.</td></tr>";
		}
		?>
	</tbody>
</table>

<script>
document.addEventListener("DOMContentLoaded", function() {
	const btn = document.getElementById("btn-backup");
	const progress = document.getElementById("progress-container");
	let esperandoDescarga = false;

	btn.addEventListener("click", function() {
		if (confirm("¿Deseas generar un respaldo ahora?")) {
			btn.disabled = true;
			progress.style.display = "block";
			esperandoDescarga = true;

			// Crear y activar enlace de descarga
			const link = document.createElement("a");
			link.href = "<?php echo SERVERURL; ?>core/respaldo.php";
			link.download = "";
			link.style.display = "none";
			document.body.appendChild(link);
			link.click();
		}
	});

	// Detectar cuando vuelve el foco a la ventana
	window.addEventListener("focus", function() {
		if (esperandoDescarga) {
			// Esperamos medio segundo para asegurarnos
			setTimeout(() => {
				location.reload(); // Recargar solo después de que se cierre la descarga
			}, 500);
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
