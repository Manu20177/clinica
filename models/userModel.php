<?php
	if($actionsRequired){
		require_once "../core/mainModel.php";
	}else{ 
		require_once "./core/mainModel.php";
	}

	class userModel extends mainModel{

		/*----------  Add user Model  ----------*/
		public function add_user_model($data){
			$query=self::connect()->prepare("INSERT INTO usuarios(Codigo,Nombres,Apellidos,Email,Cedula,Telefono,Tipo,Nivel,Provincia,Canton,Parroquia) VALUES(:Codigo,:Nombres,:Apellidos,:Email,:Cedula,:Telefono,:Tipo,:Nivel,:Provincia,:Canton,:Parroquia)");
			$query->bindParam(":Codigo",$data['Codigo']);
			$query->bindParam(":Nombres",$data['Nombres']);
			$query->bindParam(":Apellidos",$data['Apellidos']);
			$query->bindParam(":Email",$data['Email']);
			$query->bindParam(":Cedula",$data['Cedula']);
			$query->bindParam(":Telefono",$data['Telefono']);
			$query->bindParam(":Tipo",$data['Tipousu']);
			$query->bindParam(":Nivel",$data['Nivel']);
			$query->bindParam(":Provincia",$data['Provincia']);
			$query->bindParam(":Canton",$data['Canton']);
			$query->bindParam(":Parroquia",$data['Parroquia']);

			$query->execute();
			return $query;
		}


		/*----------  Data user Model  ----------*/
		public function data_user_model($data){
			if($data['Tipo']=="Count"){
				$query=self::connect()->prepare("SELECT Codigo FROM usuarios");
			}elseif($data['Tipo']=="Only"){
				$query=self::connect()->prepare("SELECT * FROM usuarios WHERE Codigo=:Codigo");
				$query->bindParam(":Codigo",$data['Codigo']);
			}
			$query->execute();
			return $query;
		}


		/*----------  Delete user Model  ----------*/
		public function delete_user_model($code){
			$query=self::connect()->prepare("DELETE FROM usuarios WHERE Codigo=:Codigo");
			$query->bindParam(":Codigo",$code);
			$query->execute();
			return $query;
		}


		/*----------  Update user Model  ----------*/
		public function update_user_model($data){
			$query=self::connect()->prepare("UPDATE usuarios SET Nombres=:Nombres,Apellidos=:Apellidos,Email=:Email,Cedula=:Cedula,Telefono=:Telefono,Tipo=:Tipo,Nivel=:Nivel,Provincia=:Provincia,Canton=:Canton,Parroquia=:Parroquia WHERE Codigo=:Codigo");
			$query->bindParam(":Nombres",$data['Nombres']);
			$query->bindParam(":Apellidos",$data['Apellidos']);
			$query->bindParam(":Email",$data['Email']);
			$query->bindParam(":Cedula",$data['Cedula']);
			$query->bindParam(":Telefono",$data['Telefono']);
			$query->bindParam(":Tipo",$data['Tipousu']);
			$query->bindParam(":Codigo",$data['Codigo']);
			$query->bindParam(":Nivel",$data['Nivel']);
			$query->bindParam(":Provincia",$data['Provincia']);
			$query->bindParam(":Canton",$data['Canton']);
			$query->bindParam(":Parroquia",$data['Parroquia']);
	
			$query->execute();
			return $query;
		}
				/*----------  Load Provinces Model  ----------*/
		public function get_provinces_model() {
			$query = self::connect()->prepare("SELECT id_provincia, nombre FROM provincias ORDER BY nombre ASC");
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}

		/*----------  Load Cantons by Province Model  ----------*/
		public function get_cantons_by_province_model($id_provincia) {
			$query = self::connect()->prepare("SELECT id_canton, nombre FROM cantones WHERE id_provincia = ?");
			$query->bindParam(1, $id_provincia, PDO::PARAM_INT);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}

		/*----------  Load Parishes by Canton Model  ----------*/
		public function get_parishes_by_canton_model($id_canton) {
			$query = self::connect()->prepare("SELECT id_parroquia, nombre FROM parroquias WHERE id_canton = ?");
			$query->bindParam(1, $id_canton, PDO::PARAM_INT);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
	}