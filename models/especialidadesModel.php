<?php
if($actionsRequired){
	require_once "../core/mainModel.php";
}else{ 
	require_once "./core/mainModel.php";
}

class especialidadesModel extends mainModel{

	/* ========== Insertar ========== */
	protected function add_especialidad_model($data){
		$pdo = self::connect();
		$sql = "INSERT INTO especialidades (nombre, estado, creado_en) VALUES (:nombre, :estado, NOW())";
		$q = $pdo->prepare($sql);
		$q->bindParam(":nombre",$data["Nombre"]);
		$q->bindParam(":estado",$data["Estado"]);
		return $q->execute();
	}

	/* ========== Obtener (Count | Only) ========== */
	protected function data_especialidad_model($data){
		if($data["Tipo"]==="Count"){
			$q=self::connect()->prepare("SELECT COUNT(*) as total FROM especialidades");
		}elseif($data["Tipo"]==="Only"){
			$q=self::connect()->prepare("SELECT id,nombre,estado,creado_en FROM especialidades WHERE id=:id LIMIT 1");
			$q->bindParam(":id",$data["Id"],PDO::PARAM_INT);
		}else{
			return false;
		}
		$q->execute();
		return $q;
	}

	/* ========== Eliminar ========== */
	protected function delete_especialidad_model($id){
		$q=self::connect()->prepare("DELETE FROM especialidades WHERE id=:id");
		$q->bindParam(":id",$id,PDO::PARAM_INT);
		return $q->execute();
	}

	/* ========== Actualizar ========== */
	protected function update_especialidad_model($data){
		$q=self::connect()->prepare("
			UPDATE especialidades
			SET nombre=:nombre, estado=:estado
			WHERE id=:id
			LIMIT 1
		");
		$q->bindParam(":nombre",$data["Nombre"]);
		$q->bindParam(":estado",$data["Estado"]);
		$q->bindParam(":id",$data["Id"],PDO::PARAM_INT);
		return $q->execute();
	}
	/* ========== Insertar relación médico ↔ especialidad ========== */
	protected function add_medico_especialidad_model($data){
		$sql = "INSERT INTO medico_especialidad (medico_codigo, especialidad_id, estado) 
				VALUES (:medico, :especialidad, :estado)";
		$q = self::connect()->prepare($sql);
		$q->bindParam(":medico",$data["Medico"],PDO::PARAM_STR); // ahora es código, no ID
		$q->bindParam(":especialidad",$data["Especialidad"],PDO::PARAM_INT);
		$q->bindParam(":estado",$data["Estado"],PDO::PARAM_STR);
		return $q->execute();
	}

	/* ========== Listar relaciones ========== */
	protected function listado_medico_especialidad_model(){
		$sql = "SELECT me.id_especialidad,
					me.medico_codigo,
					e.nombre AS especialidad
				FROM medico_especialidad me
				INNER JOIN especialidades e ON e.id = me.especialidad_id
				ORDER BY me.medico_codigo ASC";
		return self::connect()->query($sql);
	}

	/* ========== Eliminar relación ========== */
	protected function delete_medico_especialidad_model($id){
		$q = self::connect()->prepare("DELETE FROM medico_especialidad WHERE id_especialidad=:id");
		$q->bindParam(":id",$id,PDO::PARAM_INT);
		return $q->execute();
	}


}
