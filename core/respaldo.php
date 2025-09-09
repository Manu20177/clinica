<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== "Administrador") {
    exit("Acceso denegado.");
}

require_once "../core/configAPP.php"; 

$host = SERVER;
$usuario = USER;
$password = PASS;
$nombreBD = DB;

// Carpeta donde se guardará el backup
$backupDir = __DIR__ . "/respaldo"; // Esto apunta a core/respaldo

// Crear la carpeta si no existe
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$fecha = date("Y-m-d_H-i-s");
$archivoSQL = "$backupDir/backup_{$nombreBD}_$fecha.sql";
$archivoZIP = "$backupDir/backup_{$nombreBD}_$fecha.zip";

// Generar el SQL
$comando = "mysqldump --user=$usuario --password=$password --host=$host $nombreBD > \"$archivoSQL\"";
exec($comando, $output, $return_var);

if ($return_var !== 0) {
    exit("❌ Error al generar el respaldo.");
}

// Comprimir en ZIP
$zip = new ZipArchive();
if ($zip->open($archivoZIP, ZipArchive::CREATE) === TRUE) {
    $zip->addFile($archivoSQL, basename($archivoSQL));
    $zip->close();
} else {
    unlink($archivoSQL);
    exit("❌ Error al crear el ZIP.");
}

unlink($archivoSQL); // Borra solo el .sql, deja el .zip

// Descargar ZIP directamente
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($archivoZIP) . '"');
header('Content-Length: ' . filesize($archivoZIP));
readfile($archivoZIP);

// Elimina el .zip si deseas
// unlink($archivoZIP);

exit;
?>
