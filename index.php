<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	require_once "./core/configGeneral.php";
	require_once "./controllers/viewsController.php";

	$ViewTemplate=new viewsController();
	$ViewTemplate->get_template();
/*include "demo.php";*/