<?php
if($actionsRequired){
	require_once "../models/especialidadesModel.php";
}else{ 
	require_once "./models/especialidadesModel.php";
}

class especialidadesController extends especialidadesModel{

	/* ========== Add Especialidad (coincide con tu view) ========== */
	public function add_especialidad_controller(){
		$nombre = trim(self::clean_string($_POST['nombre'] ?? ''));
		$estado = trim(self::clean_string($_POST['estado'] ?? 'Activa'));

		// Validaciones básicas
		if($nombre===""){
			return self::sweet_alert_single([
				"title"=>"Campo requerido",
				"text"=>"El nombre de la especialidad es obligatorio.",
				"type"=>"error"
			]);
		}

		if(!in_array($estado, ["Activa","Inactiva"], true)){
			$estado="Activa";
		}

		// Verificar duplicados por nombre (case-insensitive)
		$dup = self::execute_single_query("SELECT id FROM especialidades WHERE UPPER(nombre)=UPPER('".self::clean_string($nombre)."') LIMIT 1");
		if($dup && $dup->rowCount()>0){
			return self::sweet_alert_single([
				"title"=>"Duplicado",
				"text"=>"Ya existe una especialidad con ese nombre.",
				"type"=>"error"
			]);
		}

		$data=[
			"Nombre"=>$nombre,
			"Estado"=>$estado
		];

		if(self::add_especialidad_model($data)){
			unset($_POST);
			return self::sweet_alert_single([
				"title"=>"¡Registrado!",
				"text"=>"La especialidad fue creada con éxito.",
				"type"=>"success"
			]);
		}

		return self::sweet_alert_single([
			"title"=>"¡Error!",
			"text"=>"No pudimos registrar la especialidad, intenta nuevamente.",
			"type"=>"error"
		]);
	}

	/* ========== Obtener datos (Count | Only) ========== */
	public function data_especialidad_controller($Type,$Id){
		$Type=self::clean_string($Type);
		$Id  =self::clean_string($Id);

		$data=["Tipo"=>$Type,"Id"=>$Id];
		$res = self::data_especialidad_model($data);
		if($res){ return $res; }

		return self::sweet_alert_single([
			"title"=>"¡Error!",
			"text"=>"No fue posible obtener los datos solicitados.",
			"type"=>"error"
		]);
	}

	/* ========== Paginación / listado simple ========== */
	public function pagination_especialidades_controller(){
		$q = self::execute_single_query("SELECT id,nombre,estado,creado_en FROM especialidades ORDER BY creado_en DESC, id DESC");
		$rows = $q ? $q->fetchAll() : [];

		$table='
		<table id="tabla-global" class="table table-striped table-bordered">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>Nombre</th>
					<th class="text-center">Estado</th>
					<th class="text-center">Creado en</th>
					<th class="text-center">Editar</th>
					<th class="text-center">Eliminar</th>
				</tr>
			</thead>
			<tbody>
		';

		$c=1;
		foreach($rows as $r){
			$table.='
			<tr>
				<td class="text-center">'.$c.'</td>
				<td>'.htmlspecialchars($r['nombre']).'</td>
				<td class="text-center"><span class="label '.($r['estado']=='Activa'?'label-success':'label-default').'">'.$r['estado'].'</span></td>
				<td class="text-center">'.htmlspecialchars($r['creado_en']).'</td>
				<td class="text-center">
					<a href="'.SERVERURL.'especialidadesinfo/'.$r['id'].'/" class="btn btn-success btn-raised btn-xs">
						<i class="zmdi zmdi-edit"></i>
					</a>
				</td>
				<td class="text-center">
					<a href="#!" class="btn btn-danger btn-raised btn-xs btnFormsAjax" data-action="delete" data-id="del-'.$r['id'].'">
						<i class="zmdi zmdi-delete"></i>
					</a>
					<form action="" id="del-'.$r['id'].'" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="especialidadId" value="'.$r['id'].'">
					</form>
				</td>
			</tr>';
			$c++;
		}

		$table.='
			</tbody>
		</table>';

		return $table;
	}

	/* ========== Eliminar ========== */
	public function delete_especialidad_controller($id){
		$id = (int) self::clean_string($id);
		if($id<=0){
			return self::sweet_alert_single([
				"title"=>"Dato inválido",
				"text"=>"Identificador no válido.",
				"type"=>"error"
			]);
		}

		// (Opcional) Verifica dependencias antes de borrar, si aplica.
		if(self::delete_especialidad_model($id)){
			return self::sweet_alert_single([
				"title"=>"¡Eliminado!",
				"text"=>"La especialidad fue eliminada correctamente.",
				"type"=>"success"
			]);
		}

		return self::sweet_alert_single([
			"title"=>"¡Error!",
			"text"=>"No pudimos eliminar la especialidad. Intenta nuevamente.",
			"type"=>"error"
		]);
	}

	/* ========== Actualizar ========== */
	public function update_especialidad_controller(){
		$id     = (int) self::clean_string($_POST['id_especialidad'] ?? '0');
		$nombre = trim(self::clean_string($_POST['nombre'] ?? ''));
		$estado = trim(self::clean_string($_POST['estado'] ?? 'Activa'));

		if($id<=0 || $nombre===""){
			return self::sweet_alert_single([
				"title"=>"Campos requeridos",
				"text"=>"Faltan datos obligatorios para actualizar.",
				"type"=>"error"
			]);
		}
		if(!in_array($estado, ["Activa","Inactiva"], true)){
			$estado="Activa";
		}

		// Verifica duplicado de nombre en otra fila
		$dup = self::execute_single_query("
			SELECT id FROM especialidades 
			WHERE UPPER(nombre)=UPPER('".self::clean_string($nombre)."') AND id<>".$id." 
			LIMIT 1
		");
		if($dup && $dup->rowCount()>0){
			return self::sweet_alert_single([
				"title"=>"Duplicado",
				"text"=>"Ya existe otra especialidad con ese nombre.",
				"type"=>"error"
			]);
		}

		$data=[
			"Id"=>$id,
			"Nombre"=>$nombre,
			"Estado"=>$estado
		];

		if(self::update_especialidad_model($data)){
			return self::sweet_alert_single([
				"title"=>"¡Actualizado!",
				"text"=>"La especialidad fue actualizada correctamente.",
				"type"=>"success"
			]);
		}

		return self::sweet_alert_single([
			"title"=>"¡Error!",
			"text"=>"No pudimos actualizar la especialidad, intenta nuevamente.",
			"type"=>"error"
		]);
	}


	/* ========== Relacionar médico ↔ especialidad ========== */
	public function add_medico_especialidad_controller(){
		$medico_codigo   = self::clean_string($_POST['medico_codigo'] ?? '');
		$especialidad_id = (int) self::clean_string($_POST['especialidad_id'] ?? 0);
		$estado = "Activo";

		if($medico_codigo==="" || $especialidad_id<=0){
			return self::sweet_alert_single([
				"title"=>"Datos incompletos",
				"text"=>"Debes ingresar el código del médico y seleccionar una especialidad.",
				"type"=>"error"
			]);
		}

		// Validar duplicado
		$dup = self::execute_single_query("
			SELECT id_especialidad FROM medico_especialidad 
			WHERE medico_codigo='$medico_codigo' AND especialidad_id=$especialidad_id
			LIMIT 1
		");
		if($dup && $dup->rowCount()>0){
			return self::sweet_alert_single([
				"title"=>"Duplicado",
				"text"=>"El médico ya tiene asignada esta especialidad.",
				"type"=>"error"
			]);
		}

		$data=["Medico"=>$medico_codigo,"Especialidad"=>$especialidad_id,"Estado"=>$estado];
		if(self::add_medico_especialidad_model($data)){
			return self::sweet_alert_single([
				"title"=>"¡Relacionada!",
				"text"=>"Especialidad asignada correctamente al médico.",
				"type"=>"success"
			]);
		}

		return self::sweet_alert_single([
			"title"=>"Error",
			"text"=>"No se pudo asignar la especialidad.",
			"type"=>"error"
		]);
	}

	/* ========== Listado relaciones ========== */
	/* ========== Listado relaciones ========== */
	public function listado_medico_especialidad_controller(){
		// JOIN para mostrar nombres de médico, cédula y nombre de especialidad
		$q = self::execute_single_query("
			SELECT me.id_especialidad,me.medico_codigo, me.especialidad_id,me.estado, u.Nombres, u.Apellidos, u.Cedula, e.nombre AS especialidad FROM medico_especialidad me INNER JOIN usuarios u ON u.Codigo = me.medico_codigo INNER JOIN especialidades e ON e.id = me.especialidad_id ORDER BY u.Apellidos ASC, u.Nombres ASC, e.nombre ASC;


		");
		$rows = $q ? $q->fetchAll() : [];

		$table='
		<table id="tabla-global" class="table table-striped table-bordered">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>Cédula</th>
					<th>Médico (Código)</th>
					<th>Especialidad</th>
					<th class="text-center">Estado</th>
					<th class="text-center">Acción</th>
				</tr>
			</thead>
			<tbody>';

		$c=1;
		foreach($rows as $r){
			$medicoNom = htmlspecialchars(trim(($r['Apellidos']??'').' '.($r['Nombres']??'')));
			$codigo    = htmlspecialchars($r['medico_codigo']);
			$cedula    = htmlspecialchars($r['Cedula']);
			$esp       = htmlspecialchars($r['especialidad']);
			$estadoLbl = ($r['estado']==='Activo' ? 'label-success' : 'label-default');

			$table.='
			<tr>
				<td class="text-center">'.$c.'</td>
				<td>'.$cedula.'</td>
				<td>'.$medicoNom.' ('.$codigo.')</td>
				<td>'.$esp.'</td>
				<td class="text-center"><span class="label '.$estadoLbl.'">'.htmlspecialchars($r['estado']).'</span></td>
				
				<td class="text-center">
					<a href="#!" class="btn btn-danger btn-raised btn-xs btnFormsAjax" data-action="delete" data-id="del-'.$r['id_especialidad'].'">
						<i class="zmdi zmdi-delete"></i>
					</a>
					<form action="" id="del-'.$r['id_especialidad'].'" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="especialidadId" value="'.$r['id_especialidad'].'">
					</form>
				</td>
			</tr>';
			$c++;
		}

		if($c===1){
			$table.='<tr><td colspan="6" class="text-center">No hay asignaciones registradas.</td></tr>';
		}

		$table.='
			</tbody>
		</table>';

		return $table;
	}


	/* ========== Delete relación ========== */
	public function delete_medico_especialidad_controller($id){
		$id = (int) self::clean_string($id);
		if($id<=0){
			return self::sweet_alert_single([
				"title"=>"Dato inválido",
				"text"=>"Identificador no válido.",
				"type"=>"error"
			]);
		}

		if(self::delete_medico_especialidad_model($id)){
			return self::sweet_alert_single([
				"title"=>"¡Eliminada!",
				"text"=>"La relación fue eliminada correctamente.",
				"type"=>"success"
			]);
		}

		return self::sweet_alert_single([
			"title"=>"Error",
			"text"=>"No se pudo eliminar la relación.",
			"type"=>"error"
		]);
	}
}
