<?php



    if ($_POST['nombre'] != '')
    {

        $mensaje = "
            <div class=\"alert alert-primary\" role=\"alert\">
                ¡Hola, {$_POST['nombre']}!
            </div>
        ";

        if (strlen($_POST['nombre']) < 5)
        {
            $mensaje = "
                <div class=\"alert alert-danger\" role=\"alert\">
                    El número de caracteres \"{$_POST['nombre']}\" debe ser superior o igual a 5.
                </div>
            ";
        }

    }


?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>
<body>


    <div class="container">
        <?php echo $mensaje; ?>
        <form action="formularioResuelto.php" method="POST" >
            <div class="mb-3">
            <label for="idNombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" id="idNombre" placeholder="Nombre..">
            </div>
            <input type="submit" class="btn btn-primary" />
        </form>
            
    </div>
</body>
</html>