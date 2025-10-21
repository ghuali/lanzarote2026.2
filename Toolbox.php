#!/usr/bin/php
<?php

function leerArgs(array $argv): array {
    $args = array_slice($argv, 1);
    if (count($args) === 0) {
        return [];
    }
    $comando = $args[0] ?? '';
    $argumentos = array_slice($args, 1);
    return [
        'comando' => $comando,
        'argumentos' => $argumentos
    ];
}

function imprimirUso(): void {
    $uso = <<<EOT
> Uso: php toolbox.php <comando> [args]
> Comandos: saludar, sumar, sumar-todos, es-primo, palabra-mas-larga, estadisticas
> Ejemplos:
>   php toolbox.php saludar Ana
>   php toolbox.php sumar 3 9
>   php toolbox.php sumar-todos 1 2 3 4
>   php toolbox.php es-primo 17
>   php toolbox.php palabra-mas-larga "Frase de ejemplo"
>   php toolbox.php estadisticas 10 2 8 4
EOT;
        echo $uso . "\n";
}

function saludar($nombre) {
    return "Hola, $nombre";
}

function sumar(int $a,int $b) {
    return $a + $b;
}

function sumarTodos(...$numeros) {
    if (count($numeros) == 0) {
        return 0;
    }
    
    return array_sum($numeros);

}

function esPrimo($n) {
    if ($n <= 1) {
        return false; // Los números menores o iguales a 1 no son primos
    }
    for ($i = 2; $i <= sqrt($n); $i++) {
        if ($numero % $i == 0) {
            return false; // Si es divisible por cualquier número, no es primo
        }
    }
    return true; // Si no encontró divisores, es primo
}

function palabraMasLarga($frase) {
    $palabras = explode(" ", $frase);
    $masLarga = "";
    foreach ($palabras as $palabra) {
        if (strlen($palabra) > strlen($masLarga)) {
            $masLarga = $palabra;
        }
    }
    return $masLarga;
}

function estadisticas(float ...$numeros) {
    if (count($numeros) == 0) {
        return null;
    }
    $min = min($numeros);
    $max = max($numeros);
    $media = array_sum($numeros) / count($numeros);
    return [
        'min' => $min,
        'max' => $max,
        'media' => $media
    ];
}