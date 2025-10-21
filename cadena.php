<?php

    $texto = trim(" Programar en PHP es divertido ");


    $numero_caracteres = strlen($texto);


    $numero_palabras = str_word_count($texto);


    $primera_letra = strtoupper(substr($texto,0,1)); 
    $ultima_letra  = strtoupper(substr($texto,-1)); 

    $texto_revertido = strrev($texto);


echo "


Para el texto: \"{$texto}\"<br />

Número de caracteres: {$numero_caracteres}<br />

Número de palabras: {$numero_palabras}<br />

Primera letra: {$primera_letra}<br />

Última letra: {$ultima_letra}<br />

Cadena invertida: {$texto_revertido}

";