<?php
	if($actionsRequired){
		require_once "../models/sucursalModel.php";
	}else{ 
		require_once "./models/sucursalModel.php";
	}

	class sucursalController extends sucursalModel{

		/*----------  Add sucursal Controller  ----------*/
		public function add_sucursal_controller(){
			$name=self::clean_string($_POST['name']);
			$direccion=self::clean_string($_POST['direccion']);
			$ciudad=self::clean_string($_POST['ciudad']);
			$cell=self::clean_string($_POST['cell']);
			$correo=self::clean_string($_POST['correo']);
			$tipo=self::clean_string($_POST['tipo']);

			$code=self::generate_code("SUC",2,"N");

		
			$datasucursal=[
				"Codigo"=>$code,
				"Nombres"=>$name,
				"Direccion"=>$direccion,
				"Ciudad"=>$ciudad,
				"Cell"=>$cell,
				"Correo"=>$correo,
				"Tipo"=>$tipo
			];

			if(self::add_sucursal_model($datasucursal)){
				$dataAlert=[
					"title"=>"Sucursal registrada!",
					"text"=>"La Sucursal se registró con éxito en el sistema",
					"type"=>"success"
				];
				unset($_POST);
				return self::sweet_alert_single($dataAlert);
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"No hemos podido registrar la Sucursal, por favor intente nuevamente",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}


		/*----------  Data sucursal Controller  ----------*/
		public function data_sucursal_controller($Type,$Code){
			$Type=self::clean_string($Type);
			$Code=self::clean_string($Code);

			$data=[
				"Tipo"=>$Type,
				"Codigo"=>$Code
			];

			if($sucursaldata=self::data_sucursal_model($data)){
				return $sucursaldata;
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"No hemos podido seleccionar los datos del sucursalistrador",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}

		}

		/*----------  Pagination sucursal Controller  ----------*/
	
		public function pagination_sucursal_controller(){
	
			$Datos=self::execute_single_query("
				SELECT * FROM sucursales WHERE 1=1 ORDER BY nombre ASC;
			");
			$Datos=$Datos->fetchAll();

			$table='
			<table id="tabla-global" class="table table-striped table-bordered">
				<thead>
					<tr>
					<th class="text-center">#</th>
						<th style="display:none;" class="text-center">Id_Sucursal</th>
						<th class="text-center">Nombres</th>
						<th class="text-center">Direccion</th>
	
						<!-- Columnas ocultas -->
						<th style="display:none;">Ciudad</th>
						<th style="display:none;">Telefono / Celular</th>
						<th style="display:none;">Email</th>
						<th class="text-center">Tipo</th>
						<th class="text-center">Estado</th>
						<th style="display:none;">Fecha de Registro</th>
						<th class="text-center">A. Datos</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
			';

			$cont=1;

			foreach($Datos as $rows){
					$table.='
					<tr>
						<td>'.$cont.'</td>
						<td style="display:none;">'.$rows['id_suc'].'</td>
						<td>'.$rows['nombre'].'</td>
						<td>'.$rows['direccion'].'</td>
						<td style="display:none;">' . $rows['ciudad'] . '</td>
						<td style="display:none;">' . $rows['telefono'] . '</td>
						<td style="display:none;">' . $rows['email'] . '</td>
						<td>'.(($rows['es_matriz'] == 1) ? 'Matriz' : 'Sucursal').'</td>
						<td>'.
							(($rows['estado'] == 'Activo') 
								? '<span class="badge bg-success" style="border-radius:12px;background-color:green; padding:5px 10px;">Activa</span>' 
								: '<span class="badge bg-danger" style="border-radius:12px;background-color:red; padding:5px 10px;">Inactiva</span>'
							)
						.'</td>
						 <!-- Columnas ocultas -->
						<td style="display:none;">' . $rows['fecha_registro'] . '</td>
						
								
						<td>
							<a href="'.SERVERURL.'sucursalinfo/'.$rows['id_suc'].'/" class="btn btn-success btn-raised btn-xs">
								<i class="zmdi zmdi-refresh"></i>
							</a>
						</td>

						<td>
							 <a href="#!" 
							class="btn btn-sm btnFormsAjax '.($rows['estado']=='Activo' ? 'btn-danger' : 'btn-success').'" 
							style="border-radius:20px;" 
							data-action="toggleEstado" 
							data-id="estado-'.$rows['id_suc'].'">'
							.($rows['estado']=='Activo' ? 'Desactivar' : 'Activar').
							'</a>



					
							<form action="" id="estado-'.$rows['id_suc'].'" method="POST">
								<input type="hidden" name="sucursalCode" value="'.$rows['id_suc'].'">
								<input type="hidden" name="sucursalEstado" value="'.$rows['estado'].'">
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


		/*----------  Delete sucursal Controller  ----------*/
		public function delete_sucursal_controller($code){
			$code=self::clean_string($code);

			if(self::delete_account($code) && self::delete_sucursal_model($code)){
				$dataAlert=[
					"title"=>"¡sucursalistrador eliminado!",
					"text"=>"El sucursalistrador ha sido eliminado del sistema satisfactoriamente",
					"type"=>"success"
				];
				return self::sweet_alert_single($dataAlert);
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"No pudimos eliminar el sucursalistrador por favor intente nuevamente",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}

		public function toggle_estado_sucursal_controller($id, $estado){
			$nuevoEstado = ($estado == 'Activo') ? 'Inactivo' : 'Activo';
			$query = mainModel::connect()->prepare("UPDATE sucursales SET estado=:estado WHERE id_suc=:id");
			$query->bindParam(":estado", $nuevoEstado);
			$query->bindParam(":id", $id);
			if($query->execute()){
				return "Estado cambiado a $nuevoEstado";
			} else {
				return "Error al cambiar estado";
			}
		}



		/*----------  Update sucursal Controller  ----------*/
		public function update_sucursal_controller(){
			$code=self::clean_string($_POST['code']);
			$name=self::clean_string($_POST['name']);
			$direccion=self::clean_string($_POST['direccion']);
			$ciudad=self::clean_string($_POST['ciudad']);
			$cell=self::clean_string($_POST['cell']);
			$correo=self::clean_string($_POST['correo']);
			$tipo=self::clean_string($_POST['tipo']);
			$estado=self::clean_string($_POST['estado']);

			
		
			$data=[
				"Codigo"=>$code,
				"Nombres"=>$name,
				"Direccion"=>$direccion,
				"Ciudad"=>$ciudad,
				"Cell"=>$cell,
				"Correo"=>$correo,
				"Estado"=>$estado,
				"Tipo"=>$tipo
			];

			if(self::update_sucursal_model($data)){
				$dataAlert=[
					"title"=>"Sucursal actualizada!",
					"text"=>"Los datos de la Sucursal fueron actualizados con éxito",
					"type"=>"success"
				];
				return self::sweet_alert_single($dataAlert);
			}else{
				$dataAlert=[
					"title"=>"¡Ocurrió un error inesperado!",
					"text"=>"No hemos podido actualizar los datos de la Sucursal, por favor intente nuevamente",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}
	}