#!/usr/bin/php
<?php

$texto = "¡Programar en PHP es divertidísimo, Ñandú!";
$k = 7;
$alfabeto = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
$longitud = strlen($alfabeto);


    // Normalización

// Normaliza tildes, deja la ñ
$normalizado = strtr($texto, "ÁÉÍÓÚÜáéíóúü", "AEIOUUaeiouu");
$textoNormalizado = strtoupper($normalizado);


function cifradoCesar($texto, $k) {
    $alfabeto = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
    $textoNormalizado = strtoupper(strtr($texto, "ÁÉÍÓÚÜáéíóúü", "AEIOUUaeiouu"));
    $resultado = "";

    $longitudAlfabeto = strlen($alfabeto);

    for ($i = 0; $i < strlen($textoNormalizado); $i++) {
        $letra = $textoNormalizado[$i];
        $pos = strpos($alfabeto, $letra);

        if ($pos !== false) {
            $nueva_pos = ($pos + $k) % $longitudAlfabeto;
            $resultado .= $alfabeto[$nueva_pos];
        } else {
            $resultado .= $letra;
        }
    }
    return $resultado;
}


function descifradoCesar($texto, $k) {
    $alfabeto = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
    $resultado = "";
    $longitudAlfabeto = strlen($alfabeto);

    for ($i = 0; $i < strlen($texto); $i++) {
        $letra = $texto[$i];
        $pos = strpos($alfabeto, $letra);
        if ($pos !== false) {
            $nueva_pos = ($pos - $k) % $longitudAlfabeto;
            if ($nueva_pos < 0) $nueva_pos += $longitudAlfabeto;
            $resultado .= $alfabeto[$nueva_pos];
        } else {
            $resultado .= $letra;
        }
    }
    return $resultado;
}

$mensaje_cifrado =  cifradoCesar($texto,$k);
$mensaje_descifrado = descifradoCesar($mensaje_cifrado,$k);

echo "
Original: {$texto}
Normalizado: {$normalizado}
Cifrado (K=7): {$mensaje_cifrado}
Descifrado: {$mensaje_descifrado}
Verificación: OK
";
