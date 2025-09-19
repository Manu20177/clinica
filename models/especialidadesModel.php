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
}
