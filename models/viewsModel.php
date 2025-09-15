<?php 
	class viewsModel{
		public function get_views_model($views){
			if(
				$views=="homeMedico" ||
				$views=="homeSecre" ||
				$views=="dashboard" ||
				$views=="admin" ||
				$views=="adminlist" ||
				$views=="admininfo" ||
				$views=="account" ||
				$views=="user" ||
				$views=="userlist" ||
				$views=="userinfo" ||	
				$views=="sucursal" ||	
				$views=="sucursallist" ||		
				$views=="sucursalinfo" ||		
				$views=="paciente" ||
				$views=="pacientelist" ||
				$views=="pacienteinfo" ||	
				
			
				$views=="backup" ||	
				

							
				$views=="search"
			){
				if(is_file("./views/contents/".$views."-view.php")){
					$contents="./views/contents/".$views."-view.php";
				}else{
					$contents="login";
				}
			}elseif($views=="index"){
				$contents="login";
			}elseif($views=="login"){
				$contents="login";
			}else{
				$contents="login";
			}
			return $contents;
		}
	}