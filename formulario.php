<?php
    $nombre = $_POST['nombre'] ?? 'No definido';
    if($nombre != NULL and strlen($nombre) >= 5){
    echo "Nombre: $nombre <br>";
    } else {
        echo "Skill issue <br>";
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
    <form action="formulario.php" method="post">
        <label>Nombre: <input type="text" name="nombre"></label><br>
        
        <button type="submit">Enviar por POST</button>
    </form>
</body>
</html>