<?php

class Tutor extends Base
{
    function __construct()
    {
        $this->tabla = 'tutores';
    }

    function get_horario($id_tutor)
    {
        // Aquí va la lógica para traer los módulos del tutor
        // Ejemplo:
        return $this->ejecutar_sql("SELECT * FROM modulos WHERE id_tutor = $id_tutor");
    }
}
