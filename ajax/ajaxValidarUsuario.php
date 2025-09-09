<?php
	$actionsRequired=true;
	require_once "../controllers/userController.php";

	$insVideo = new userController();

	

	if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $existe = $insVideo->validarUsuario($username);

    echo json_encode(['disponible' => !$existe]); // true = disponible
}

// $username = "manu";

// 		$row=$insVideo->validarUsuario($username);
// 		if ($row> 0) {
// 			echo json_encode(['disponible' => false]);
// 		} else {
// 			echo json_encode(['disponible' => true]);
// 		}
