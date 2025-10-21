<?php

$email  = $_POST['email'];
$nombre = $_POST['nombre'];
$telefono  = $_POST['telefono'];

$nombre = $_POST['nombre'] ?? 'No definido';
    
$patronNombre = "/^[A-ZÁÉÍÓÚ][a-záéíóú]+/";
if (preg_match($patronNombre,$nombre)){
    echo "Nombre: $nombre <br>";
    } else {
        echo "Campo de nombre incorrecto<br>
        Nombre: $nombre <br>";
    }

$email = $_POST['email'] ?? 'No definido';

$patronEmail = "/^[\w\-\.]+@([\w\-]+\.)+[a-zA-Z]{2,7}$/";
if ((preg_match($patronEmail, $email)) and $email != NULL) {
    echo "Email: $email <br>";
    } else {
    echo "Campo de Email inválido <br>
    Email: $email <br>";
};


$telefono = $_POST['telefono'] ?? 'No definido';

$patronTelefono = "/[1-9]{9,15}$/";
if ((preg_match($patronTelefono,$numero)) and $telefono != NULL) {
    echo "Telefono Valido $telefono <br>";
} else {
    echo "Telefono Invalido <br>
    Telefono: $telefono <br>";
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="examen1Angel.php" method="post">
        <label>Nombre: <input type="text" name="nombre"></label><br>
        
        
    </form>
    <form action="examen1Angel.php" method="post">
        <label>Correo: <input type="text" name="email"></label><br>
        
    </form>
    <form action="examen1Angel.php" method="post">
        <label>Telefono: <input type="text" name="telefono"></label><br>
        
        <button type="submit">Enviar por POST</button>
    </form>
</body>
</html>