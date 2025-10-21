<?php
$email = "usuario@ejemplo.com";
$patron = "/^[\w\-\.]+@([\w\-]+\.)+[a-zA-Z]{2,7}$/";
if (preg_match($patron, $email)) {
echo "Email válido";
} else {
echo "Email inválido";
}


$numero= 1111111111111111;
$patronTelefono = "/([1-9]){9,15}$/";
if (preg_match($patronTelefono,$numero)) {
    echo "Telefono Valido";
} else {
    echo "Telefono Invalido";
};

$nombre = "Ghuali1234_67";
$patronNombre = "/^[A-ZÁÉÍÓÚ][a-záéíóú]+/";
if (preg_match($patronNombre,$nombre)){
    echo "Nombre válido";
    } else {
        echo "Nombre inválido";
    }
?>