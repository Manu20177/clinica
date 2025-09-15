<?php
if ($actionsRequired) {
    require_once "../core/mainModel.php";
} else {
    require_once "./core/mainModel.php";
}

class loginModel extends mainModel {

    /*---------- Modelo para iniciar sesión - Model to log in ----------*/
    public function login_session_start_model($data) {
        $query = self::connect()->prepare("SELECT c.*,e.Nombres,e.Apellidos, s.nombre as suc,s.id_suc FROM cuenta c LEFT JOIN usuarios e on e.Codigo=c.Codigo LEFT JOIN usuarios u on u.Codigo=c.Codigo LEFT JOIN sucursales s on s.id_suc=u.Sucursal WHERE c.Usuario = :Usuario AND c.Clave = :Clave");
        $query->bindParam(":Usuario", $data['Usuario']);
        $query->bindParam(":Clave", $data['Clave']);
        $query->execute();
        return $query;
    }

    /*---------- Modelo para verificar token (opcional) - Optional token check model ----------*/
    public function check_user_token_model($username, $token) {
        $query = self::connect()->prepare("SELECT COUNT(*) FROM cuenta WHERE Usuario = :Usuario AND Token = :Token");
        $query->bindParam(":Usuario", $username);
        $query->bindParam(":Token", $token);
        $query->execute();
        return $query->fetchColumn() > 0;
    }
    /* Modelo para destruir sesion - Model to destroy session*/
		public function login_session_destroy_model($data){
			if($data['userName']!="" && $data['userToken']==$data['token']){
				session_destroy();
				return true;
			}else{
				return false;
			}
		}

}