<?php
	// define('SERVERURL', 'https://plantilla.mmtechsolutions.io/'); 
	// define('SERVER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
	const SERVERURL = "http://localhost/clinica/";
	define('SERVER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/clinica/');

	define('USER_ADMIN', 'Administrador');
    define('USER_SECRETARIA', 'Secretaria');
    define('USER_MEDICO', 'Medico');


	//const COMPANY = "Mi Aula Credito Familiar";
	const COMPANY = "CliniPro";

	/*====================================
	=            Zona horaria            =
	====================================*/
	date_default_timezone_set("America/Guayaquil");

	/**
		Zonas horarias:
		- America/El_Salvador
		- America/Costa_Rica
		- America/Guatemala
		- America/Puerto_Rico
		- America/Panama
		- Europe/Madrid

		Más zonas, visita http://php.net/manual/es/timezones.php

	/*=====  End of Zona horaria  ======*/