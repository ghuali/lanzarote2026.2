<?php
    session_start();


    $nombre = $_POST['nombre'];

    $_SESSION['usuario'] = $nombre;

    if ($_SESSION['usuario'] == $nombre) {
        header("Location: Bienvenido.php");
        echo($_SESSION['usuario']);
        var_dump($_SESSION['usuario']);
    }
    


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="login.php" method="post">
        <label>Nombre: <input type="text" name="nombre"></label><br>
        
        <button type="submit">Enviar por POST</button>
    </form>
</body>
</html>