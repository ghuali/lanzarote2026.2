<?php
    session_start();

    var_dump($_SESSION['usuario']);
    $_SESSION['usuario'] = $_POST['nombre'];
    if (($_SESSION['usuario']) != NULL) {
        echo "Hola " . $_SESSION['usuario'];
    } else {
        header("Location: login.php");
    }
?>
