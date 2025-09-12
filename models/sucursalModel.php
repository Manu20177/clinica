<?php
	if($actionsRequired){
		require_once "../core/mainModel.php";
	}else{ 
		require_once "./core/mainModel.php";
	}

	class sucursalModel extends mainModel{

		/*----------  Add sucursal Model  ----------*/
		public function add_sucursal_model($data){
			$estado="Activa";
			$query=self::connect()->prepare("INSERT INTO sucursales(id_suc,nombre,direccion,ciudad,telefono,email,es_matriz,estado) VALUES(:Codigo,:Nombres,:Direccion,:Ciudad,:Cell,:Correo,:Tipo,:Estado)");
			$query->bindParam(":Codigo",$data['Codigo']);
			$query->bindParam(":Nombres",$data['Nombres']);
			$query->bindParam(":Direccion",$data['Direccion']);
			$query->bindParam(":Ciudad",$data['Ciudad']);
			$query->bindParam(":Cell",$data['Cell']);
			$query->bindParam(":Correo",$data['Correo']);
			$query->bindParam(":Tipo",$data['Tipo']);
			$query->bindParam(":Estado",$estado);
			$query->execute();
			return $query;
		}

		/*----------  Data sucursal Model  ----------*/
		public function data_sucursal_model($data){
			if($data['Tipo']=="Count"){
				$query=self::connect()->prepare("SELECT id_suc FROM sucursales");
			}elseif($data['Tipo']=="Only"){
				$query=self::connect()->prepare("SELECT * FROM sucursales WHERE id_suc=:Codigo");
				$query->bindParam(":Codigo",$data['Codigo']);
			}
			$query->execute();
			return $query;
		}

		/*----------  Delete sucursal Model  ----------*/
		public function delete_sucursal_model($code){
			$query=self::connect()->prepare("DELETE FROM sucursal WHERE Codigo=:Codigo");
			$query->bindParam(":Codigo",$code);
			$query->execute();
			return $query;
		}

		/*----------  Update sucursal Model  ----------*/
		public function update_sucursal_model($data){
			$query=self::connect()->prepare("UPDATE sucursales SET nombre=:Nombres,direccion=:Direccion,ciudad=:Ciudad,telefono=:Cell,email=:Correo,es_matriz=:Tipo,estado=:Estado WHERE id_suc=:Codigo");
			$query->bindParam(":Codigo",$data['Codigo']);
			$query->bindParam(":Nombres",$data['Nombres']);
			$query->bindParam(":Direccion",$data['Direccion']);
			$query->bindParam(":Ciudad",$data['Ciudad']);
			$query->bindParam(":Cell",$data['Cell']);
			$query->bindParam(":Correo",$data['Correo']);
			$query->bindParam(":Tipo",$data['Tipo']);
			$query->bindParam(":Estado",$data['Estado']);
			$query->execute();
			return $query;
		}
	}