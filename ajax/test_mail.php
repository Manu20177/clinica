<?php
$to = "manuelmacias698@gmail.com";
$subject = "Prueba de mail() en producción";
$message = "Este es un correo de prueba desde el servidor usando mail().";
$headers = "From: no-reply@tudominio.com\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Correo enviado correctamente.";
} else {
    echo "Error al enviar el correo.";
}