<?php



spl_autoload_register(function ($class) {

    switch($class)
    {
        case 'Query':
            require_once "bbdd/query.php";
        break;
        case 'BBDD':
            require_once "bbdd/bbdd.php";
        break;
        case 'Template':
            require_once "template.php";
        break;
        case 'Idioma':
            require_once "idioma.php";
        break;
    }

});