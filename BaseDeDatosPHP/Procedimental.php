<?php
$servidor = "localhost";
$usuario = "lanzarote";
$contraseña = "Ghuali21!";
$base_datos = "gestion_usuarios";
$conexion = mysqli_connect($servidor, $usuario, $contraseña,
$base_datos);
if (!$conexion) {
die("Conexión fallida: " . mysqli_connect_error());
}
echo "Conexión exitosa";