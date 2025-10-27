<?php
if (isset($_GET['pagina']) && $_GET['pagina'] != "") {
    header("Location: " . $_GET['pagina']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilos.css">
    <title>Document</title>
</head>
<body>
    <div class="container">
    <form action="" method="get">
        <select name="pagina">
            <option value="">Abre para ver las opciones</option>
            <option value="Procedimental.php">Procedimental</option>
            <option value="orientado_objetos.php">Orientado a objetos</option>
            <option value="pdo.php">PDO</option>
        </select>
        <button type="submit">Ir</button>
    </form>
</div> 
</body>
</html>