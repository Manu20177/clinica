<?php
	if($actionsRequired){
		require_once "../models/loginModel.php";
	}else{ 
		require_once "./models/loginModel.php";
	}

	class loginController extends loginModel{

		/* Controlador para iniciar sesion - Controller to log in*/
		public function login_session_start_controller(){

			$userName=self::clean_string($_POST['loginUserName']);
			$userPass=self::clean_string($_POST['loginUserPass']);

			$userPass=self::encryption($userPass);

			$data=[
				"Usuario"=>$userName,
				"Clave"=>$userPass
			];

			if($dataAccount=self::login_session_start_model($data)){
				if($dataAccount->rowCount()==1){
					$row=$dataAccount->fetch();
					session_start();
					$name=$row['Nombres']." ".$row['Apellidos'];
					$_SESSION['userNombre']=$name;
					$_SESSION['userName']=$row['Usuario'];
					$_SESSION['userType']=$row['Tipo'];
					$_SESSION['userIdSuc']=$row['id_suc'];
					$_SESSION['userSuc']=$row['suc'];
					$_SESSION['userKey']=$row['Codigo'];
					$_SESSION['userPrivilege']=$row['Privilegio'];
					$_SESSION['userToken']=md5(uniqid(mt_rand(), true));

					if($row['Tipo']=="Administrador"){
						$_SESSION['Avatar']="avatar-chef.png";
						$url=SERVERURL."dashboard/";
					}elseif($row['Tipo']=="Medico"){
						if($row['Genero']=="Masculino"){
							$_SESSION['Avatar']="avatar-user-male.png";
						}else{
							$_SESSION['Avatar']="avatar-user-female.png";
						}
						$url=SERVERURL."homeMedico/";
					}elseif($row['Tipo']=="Secretaria"){
						if($row['Genero']=="Masculino"){
							$_SESSION['Avatar']="avatar-user-male.png";
						}else{
							$_SESSION['Avatar']="avatar-user-female.png";
						}
						$url=SERVERURL."homeSecre/";
					}

					$urlLocation='<script type="text/javascript"> window.location="'.$url.'"; </script>';
					return $urlLocation;
				}else{
					$dataAlert=[
						"title"=>"Ocurrió un error inesperado",
						"text"=>"El nombre de usuario y contraseña no son correctos",
						"type"=>"error"
					];
					return self::sweet_alert_single($dataAlert);
				}
			}else{
				$dataAlert=[
					"title"=>"Ocurrió un error inesperado",
					"text"=>"No se pudo realizar la petición",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}

		/* Controlador para destruir sesion - Controller to destroy session*/
		public function login_session_destroy_controller() {
            if (!isset($_POST['token'])) {
                echo '
                    <script>
                        window.location.href = "'.SERVERURL.'login/";
                    </script>
                ';
                exit();
            }
        
            $token = $_POST['token'];
        
            if (isset($_SESSION['userToken'], $_SESSION['userName']) &&
                $_SESSION['userToken'] === $token) {
        
                // Cerrar sesión si coincide el token
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_unset();
                    session_destroy();
                }
            }
        
            echo '
                <script>
                    window.location.href = "'.SERVERURL.'login/";
                </script>
            ';
            exit();
        }

		/* Controlador para destruir sesion forzada - Controller to destroy session force*/
		public function login_session_force_destroy_controller(){
			$token=$_SESSION['userToken'];
			$data=[
				"userName"=>$_SESSION['userName'],
				"userToken"=>$_SESSION['userToken'],
				"token"=>$token
			];
			if(self::login_session_destroy_model($data)){
				$urlLocation='<script type="text/javascript"> window.location="'.SERVERURL.'login/"; </script>';
				return $urlLocation;
			}else{
				$dataAlert=[
					"title"=>"Ocurrió un error inesperado",
					"text"=>"No se pudo cerrar la sesión",
					"type"=>"error"
				];
				return self::sweet_alert_single($dataAlert);
			}
		}

		/*=== Check Access Controller ====*/
		public function check_access($userToken, $userVar){
			if(!isset($userToken) || !isset($userVar)){
				session_start();
				session_destroy();
				echo '
                    <script>
                        window.location.href = "'.SERVERURL.'login/";
                    </script>
                ';
			}
		}
	}