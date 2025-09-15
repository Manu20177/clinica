<?php
	if($actionsRequired){
		require_once "../core/mainModel.php";
	}else{ 
		require_once "./core/mainModel.php";
	}

	class pacienteModel extends mainModel{

		/*----------  Add paciente Model  ----------*/
		public function add_paciente_model($data){
			$query = self::connect()->prepare("
				INSERT INTO pacientes(
					id_paciente,
					cedula,
					nombres,
					apellidos,
					fecha_nacimiento,
					genero,
					telefono,
					correo,
					direccion,
					estado_civil,
					tipo_sangre,
					alergias,
					enfermedades,
					id_suc,
					actualizado_por	
				) VALUES(
					:Codigo,
					:Cedula,
					:Nombres,
					:Apellidos,
					:Fecha_nacimiento,
					:Genero,
					:Telefono,
					:Correo,
					:Direccion,
					:Estado_civil,
					:Tipo_sangre,
					:Alergias,
					:Enfermedades,
					:Id_suc,
					:Actualizado_por
				)
			");

			$query->bindParam(":Codigo", $data['Codigo']);
			$query->bindParam(":Nombres", $data['Nombres']);
			$query->bindParam(":Apellidos", $data['Apellidos']);
			$query->bindParam(":Cedula", $data['Cedula']);
			$query->bindParam(":Fecha_nacimiento", $data['Fecha_nacimiento']);
			$query->bindParam(":Genero", $data['Genero']);
			$query->bindParam(":Telefono", $data['Telefono']);
			$query->bindParam(":Correo", $data['Email']);
			$query->bindParam(":Direccion", $data['Direccion']);
			$query->bindParam(":Estado_civil", $data['Estado_civil']);
			$query->bindParam(":Tipo_sangre", $data['Tipo_sangre']);
			$query->bindParam(":Alergias", $data['Alergias']);
			$query->bindParam(":Enfermedades", $data['Enfermedades']);
			$query->bindParam(":Id_suc", $data['Id_suc']);
			$query->bindParam(":Actualizado_por", $data['Actualizado_por']);
			$query->execute();
			return $query;
		}


		/*----------  Data paciente Model  ----------*/
		public function data_paciente_model($data){
			if($data['Tipo']=="Count"){
				$query=self::connect()->prepare("SELECT Codigo FROM usuarios");
			}elseif($data['Tipo']=="Only"){
				$query=self::connect()->prepare("SELECT * FROM usuarios WHERE Codigo=:Codigo");
				$query->bindParam(":Codigo",$data['Codigo']);
			}
			$query->execute();
			return $query;
		}


		/*----------  Delete paciente Model  ----------*/
		public function delete_paciente_model($code){
			$query=self::connect()->prepare("DELETE FROM usuarios WHERE Codigo=:Codigo");
			$query->bindParam(":Codigo",$code);
			$query->execute();
			return $query;
		}


		/*----------  Update paciente Model  ----------*/
		public function update_paciente_model($data){
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