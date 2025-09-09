<?php
	$actionsRequired=true;

    require_once "../controllers/userController.php";

    $controller = new userController();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $controller->load_locations_controller();
    }
?>