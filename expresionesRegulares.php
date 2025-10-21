<?php

$fecha = "AWdad 16/06/2006 AWDawda";

$split = preg_split("\/",$fecha);

$patron = "/.?(0?[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0,1,2])\/(19|20)\d{2}.?/";
$patron2 = "/[A-ZÁÉÍÓÚ][a-záéíóú]+/";

$match = preg_match($patron, $fecha);

if (preg_match($patron, $fecha,$split)) {
echo "fecha válida";
echo " la fecha es:
dia: " . $split[1],
" mes: " . $split[2],
" año: " . $split[3];

} else {
echo "fecha inválida";
}

if (preg_match_all($patron2,$fecha,$coincdencia)) {

    echo "Mayusculas encontradas" . $coincdencia[0];
} else {
    echo "no hay maysculas";

}