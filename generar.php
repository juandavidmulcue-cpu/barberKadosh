<?php
$password = "Pucca1702."; // tu contraseña en texto plano

$hash = password_hash($password, PASSWORD_DEFAULT);

echo $hash;