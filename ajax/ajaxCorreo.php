<?php
    session_start(); // Importante si usas $_SESSION['userKey']
	$actionsRequired=true;

    require_once "../controllers/correoController.php";
        
    $correoCtrl = new correoController();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
     
        echo $correoCtrl->send_email_controller();
    } else {
        echo '<script>swal("Error", "Método no permitido", "error");</script>';
    }
?>