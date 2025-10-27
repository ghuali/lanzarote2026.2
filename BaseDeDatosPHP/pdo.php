<?php
$dsn = 'mysql:host=localhost;dbname=gestion_usuarios';
$usuario = 'lanzarote';
$contraseña = 'Ghuali21!';
try {
$conexion = new PDO($dsn, $usuario, $contraseña);
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Conexión exitosa";
} catch (PDOException $e) {
echo "Error en la conexión: " . $e->getMessage();
}
?>