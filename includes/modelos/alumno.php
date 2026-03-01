<?php

class Alumno extends Base
{
    function __construct()
    {
        $this->tabla = 'alumnos';
    }

    function get_modulos($id_alumno)
    {
        return $this->ejecutar_sql("
            SELECT m.nombre_modulo
            FROM modulos m
            INNER JOIN matriculas mat ON mat.id_modulo = m.id
            WHERE mat.id_alumno = {$id_alumno}
        ");
    }
}