<?php
	if($actionsRequired){
		require_once "../models/pacienteModel.php";
	}else{ 
		require_once "./models/pacienteModel.php";
	}

	class pacienteController extends pacienteModel{

		/*----------  Add paciente Controller  ----------*/
		public function add_paciente_controller(){
			$cedula             = self::clean_string($_POST['cedula']);
			$nombres            = self::clean_string($_POST['nombres']);
			$apellidos          = self::clean_string($_POST['apellidos']);
			$fecha_nacimiento   = self::clean_string($_POST['fecha_nacimiento']);
			$genero             = self::clean_string($_POST['genero']);
			$telefono           = self::clean_string($_POST['telefono']);
			$correo             = self::clean_string($_POST['correo']);
			$direccion          = self::clean_string($_POST['direccion']);
			$estado_civil       = self::clean_string($_POST['estado_civil']);
			$tipo_sangre        = self::clean_string($_POST['tipo_sangre']);
			$alergias           = self::clean_string($_POST['alergias']);
			$enfermedades       = self::clean_string($_POST['enfermedades']);
			$id_suc             = $_SESSION['userIdSuc'];
			$actualizado_por    = $_SESSION['userKey'];



			$query2=self::execute_single_query("SELECT id_paciente FROM pacientes");
			$correlative=($query2->rowCount())+1;

			$code=self::generate_code("PAC",5,$correlative);

			$datapaciente=[
				"Codigo"=>$code,
				"Nombres"=>$nombres,
				"Apellidos"=>$apellidos,
				"Email"=>$correo,
				"Cedula"=>$cedula,
				"Telefono"=>$telefono,
				"Fecha_nacimiento"=>$fecha_nacimiento,
				"Genero"=>$genero,
				"Direccion"=>$direccion,
				"Estado_civil"=>$estado_civil,
				"Tipo_sangre"=>$tipo_sangre,
				"Alergias"=>$alergias,
				"Enfermedades"=>$enfermedades,
				"Id_suc"=>$id_suc,
				"Actualizado_por"=>$actualizado_por
				
			];

			if(self::add_paciente_model($datapaciente)){
				$dataAlert=[
					"title"=>"Paciente Registrado!",
					"text"=>"El paciente se registró con éxito en el sistema",
					"type"=>"success"
				];
				unset($_POST);
				return self::sweet_alert_single($dataAlert);
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"No hemos podido registrar al paciente, por favor intente nuevamente",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}
		/*----------  Add paciente Controller  ----------*/
		public function add_acountpaciente_controller(){
			$name=self::clean_string($_POST['name']);
			$lastname=self::clean_string($_POST['lastname']);
			$gender=self::clean_string($_POST['gender']);
			$email=self::clean_string($_POST['email']);
			$cedula=self::clean_string($_POST['cedula']);
			$telefono=self::clean_string($_POST['telefono']);
			$tipousu=self::clean_string($_POST['tipousu']);
			$pacientename=self::clean_string($_POST['pacientename']);
			$password1=self::clean_string($_POST['password1']);
			$password2=self::clean_string($_POST['password2']);
			$nivel=self::clean_string($_POST['nivel']);
			$provincia=self::clean_string($_POST['provincia']);
			$canton=self::clean_string($_POST['canton']);
			$parroquia=self::clean_string($_POST['parroquia']);
			$actividad=self::clean_string($_POST['actividad']);
			$etnia=self::clean_string($_POST['etnia']);

			if ($tipousu==3) {
				# code...
				$Tipou="Secretaria";

			}else {
				# code...
				$Tipou="Medico";

			}
			if($password1!="" || $password2!=""){
				if($password1==$password2){
					$query1=self::execute_single_query("SELECT Usuario FROM cuenta WHERE Usuario='$pacientename'");
					if($query1->rowCount()<=0){
						$query2=self::execute_single_query("SELECT id FROM cuenta");
						$correlative=($query2->rowCount())+1;

						$code=self::generate_code("EC",7,$correlative);
						$password1=self::encryption($password1);

						$dataAccount=[
							"Privilegio"=>$tipousu,
							"Usuario"=>$pacientename,
							"Clave"=>$password1,
							"Tipo"=>$Tipou,
							"Genero"=>$gender,
							"Codigo"=>$code
						];

						$datapaciente=[
							"Codigo"=>$code,
							"Nombres"=>$name,
							"Apellidos"=>$lastname,
							"Email"=>$email,
							"Cedula"=>$cedula,
							"Telefono"=>$telefono,
							"Tipousu"=>$tipousu,
							"Nivel"=>$nivel,
							"Provincia"=>$provincia,
							"Canton"=>$canton,
							"Parroquia"=>$parroquia,
							"Actividad"=>$actividad,
							"Etnia"=>$etnia
							
						];

						if(self::save_account($dataAccount) && self::add_paciente_model($datapaciente)){
							$dataAlert=[
								"title"=>"¡usuario registrado!",
								"text"=>"El usuario se registró con éxito en el sistema",
								"type"=>"success"
							];
							unset($_POST);
							$url="../login";
							return self::sweet_alert_url_reload($dataAlert,$url);
						}else{
							$dataAlert=[
								"title"=>"¡Ocurrió un error inesperado!",
								"text"=>"No hemos podido registrar el usuario, por favor intente nuevamente",
								"type"=>"error"
							];
							return self::sweet_alert_single($dataAlert);
						}

					}else{
						$dataAlert=[
							"title"=>"¡Ocurrió un error inesperado!",
							"text"=>"El nombre de usuario que acaba de ingresar ya se encuentra registrado en el sistema, por favor elija otro",
							"type"=>"error"
						];
						return self::sweet_alert_single($dataAlert);
					}
				}else{
					$dataAlert=[
						"title"=>"¡Ocurrió un error inesperado!",
						"text"=>"Las contraseñas que acabas de ingresar no coinciden",
						"type"=>"error"
					];
					return self::sweet_alert_single($dataAlert);
				}
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"Debes de llenar los campos de las contraseñas para registrar el usuario",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}



		/*----------  Data paciente Controller  ----------*/
		public function data_paciente_controller($Type,$Code){
			$Type=self::clean_string($Type);
			$Code=self::clean_string($Code);

			$data=[
				"Tipo"=>$Type,
				"Codigo"=>$Code
			];

			if($pacientedata=self::data_paciente_model($data)){
				return $pacientedata;
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"No hemos podido seleccionar los datos del usuario",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}

		}

		/*----------  Data paciente Controller  ----------*/
		public function validarUsuario($paciente){
			$paciente=self::clean_string($paciente);
		
			$Total=self::execute_single_query("SELECT * FROM cuenta WHERE Usuario = '$paciente'");
			$Total=$Total->rowCount();

			return $Total > 0;

		}



		/*----------  Pagination paciente Controller  ----------*/
		public function pagination_paciente_controller(){
	
			$Datos=self::execute_single_query("
				SELECT p.*,s.nombre as sucursal,u.Nombres as n,u.Apellidos as a FROM `pacientes` p LEFT JOIN sucursales s on s.id_suc=p.id_suc LEFT JOIN usuarios u on u.Codigo=p.actualizado_por ORDER BY p.fecha_registro ASC;
			");
			$Datos=$Datos->fetchAll();

			$table='
			<table id="tabla-global" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center" style="display:none;">id_paciente</th>
						<th class="text-center">Cedula</th>
						<th class="text-center">Nombres</th>
						<th class="text-center">Apellidos</th>
						<th class="text-center">Fecha de Nacimiento</th>
						<th class="text-center" style="display:none;">Genero</th>
						<!-- Columnas ocultas -->
						<th style="display:none;">Teléfono</th>
						<th style="display:none;">Correo</th>
						<th style="display:none;"> Direccion</th>
						<th style="display:none;">Estado Civil</th>
						<th >Tipo de Sangre</th>
						<th >Alergias</th>
						<th >Enfermedades</th>
						<th >Sucursal</th>
						<th style="display:none;">Fecha de Registro</th>
						<th style="display:none;">Actualizado por</th>
						<th style="display:none;">Fecha de Actualizacion</th>
						<th class="text-center">A. Datos</th>
						<th class="text-center">Eliminar</th>
					</tr>
				</thead>
				<tbody>
			';

			$cont=1;

			foreach($Datos as $rows){
					$table.='
					<tr>
						<td>'.$cont.'</td>
						<td style="display:none;">'.$rows['id_paciente'].'</td>
						<td>'.$rows['cedula'].'</td>
						<td>'.$rows['nombres'].'</td>
						<td>'.$rows['apellidos'].'</td>
						<td>'.date("d/m/Y", strtotime($rows['fecha_nacimiento'])).'</td>
						<td style="display:none;">'.$rows['genero'].'</td>
						<td style="display:none;">'.$rows['telefono'].'</td>
						 <!-- Columnas ocultas -->
						<td style="display:none;">' . $rows['correo'] . '</td>
						<td style="display:none;">' . $rows['direccion'] . '</td>
						<td style="display:none;">' . $rows['estado_civil'] . '</td>
						<td >' . $rows['tipo_sangre'] . '</td>
						<td >' . $rows['alergias'] . '</td>
						<td >' . $rows['enfermedades'] . '</td>
						<td >' . $rows['sucursal'] . '</td>
						<td style="display:none;">' . $rows['fecha_registro'] . '</td>
						<td style="display:none;">' . $rows['n'] .' ' . $rows['a'] . '</td>
						<td style="display:none;">' . $rows['fecha_actualizacion'] . '</td>
								
						<td>
							<a href="'.SERVERURL.'pacienteinfo/'.$rows['id_paciente'].'/" class="btn btn-success btn-raised btn-xs">
								<i class="zmdi zmdi-refresh"></i>
							</a>
						</td>
						
						<td>
							<a href="#!" class="btn btn-danger btn-raised btn-xs btnFormsAjax" data-action="delete" data-id="del-'.$rows['id_paciente'].'">
								<i class="zmdi zmdi-delete"></i>
							</a>
							<form action="" id="del-'.$rows['id_paciente'].'" method="POST" enctype="multipart/form-data">
								<input type="hidden" name="pacienteCode" value="'.$rows['id_paciente'].'">
							</form>
						</td>
					</tr>
					';
					$cont++;
				}

			$table.='
				</tbody>
			</table>
			';

			

			return $table;
		}


		/*----------  Delete paciente Controller  ----------*/
		public function delete_paciente_controller($code){
			$code=self::clean_string($code);

			if(self::delete_account($code) && self::delete_paciente_model($code)){
				$dataAlert=[
					"title"=>"¡usuario eliminado!",
					"text"=>"El usuario ha sido eliminado del sistema satisfactoriamente",
					"type"=>"success"
				];
				return self::sweet_alert_single($dataAlert);
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"No pudimos eliminar el usuario por favor intente nuevamente",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}


		/*----------  Update paciente Controller  ----------*/
		public function update_paciente_controller(){
			$code=self::clean_string($_POST['code']);
			$name=self::clean_string($_POST['name']);
			$lastname=self::clean_string($_POST['lastname']);
			$email=self::clean_string($_POST['email']);
			$cedula=self::clean_string($_POST['cedula']);
			$telefono=self::clean_string($_POST['telefono']);
			$tipousu=self::clean_string($_POST['tipousu']);
			$nivel=self::clean_string($_POST['nivel']);
			$provincia=self::clean_string($_POST['provincia']);
			$canton=self::clean_string($_POST['canton']);
			$parroquia=self::clean_string($_POST['parroquia']);
	

			$data=[
				"Codigo"=>$code,
				"Nombres"=>$name,
				"Apellidos"=>$lastname,
				"Email"=>$email,
				"Cedula"=>$cedula,
				"Telefono"=>$telefono,
				"Tipousu"=>$tipousu,
				"Nivel"=>$nivel,
				"Provincia"=>$provincia,
				"Canton"=>$canton,
				"Parroquia"=>$parroquia
				
			];

			if(self::update_paciente_model($data)){
				$dataAlert=[
					"title"=>"¡usuario actualizado!",
					"text"=>"Los datos del usuario fueron actualizados con éxito",
					"type"=>"success"
				];
				return self::sweet_alert_single($dataAlert);
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"No hemos podido actualizar los datos del usuario, por favor intente nuevamente",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}
				/*----------  AJAX Load Locations Controller  ----------*/
		public function load_locations_controller() {
			if (isset($_POST['action'])) {
				switch ($_POST['action']) {
					case 'load_provinces':
						$data = self::get_provinces_model();
						echo "<option value=''>Seleccione una provincia</option>";
						foreach ($data as $prov) {
							echo "<option value='{$prov['id_provincia']}'>{$prov['nombre']}</option>";
						}
						break;

					case 'load_cantons':
						$id_provincia = $_POST['id_provincia'];
						$data = self::get_cantons_by_province_model($id_provincia);
						echo "<option value=''>Seleccione un cantón</option>";
						foreach ($data as $canton) {
							echo "<option value='{$canton['id_canton']}'>{$canton['nombre']}</option>";
						}
						break;

					case 'load_parishes':
						$id_canton = $_POST['id_canton'];
						$data = self::get_parishes_by_canton_model($id_canton);
						echo "<option value=''>Seleccione una parroquia</option>";
						foreach ($data as $parroquia) {
							echo "<option value='{$parroquia['id_parroquia']}'>{$parroquia['nombre']}</option>";
						}
						break;
				}
			}
		}

	}