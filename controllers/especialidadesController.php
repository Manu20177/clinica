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
}
