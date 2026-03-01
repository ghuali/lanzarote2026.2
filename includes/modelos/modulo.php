<?php

class Modulo extends Base
{
    function __construct()
    {
        $this->tabla = 'modulos';
    }

    function cargar()
    {
        $datos_consulta = $this->get_rows([
            'select' => 'id, nombre, codigo'
        ]);

        $modulos = [];
        foreach($datos_consulta as $registro)
        {
            $modulos[$registro['id']] = $registro['codigo'] . ' - ' . $registro['nombre'];
        }

        return $modulos;
    }

    function get_profesores($id_modulo)
    {
        return $this->ejecutar_sql("
            SELECT p.nombre, p.email
            FROM profesores p
            INNER JOIN modulo_profesores mp ON mp.id_profesor = p.id
            WHERE mp.id_modulo = {$id_modulo}
        ");
    }

    function get_alumnos($id_modulo)
    {
        return $this->ejecutar_sql("
            SELECT a.nombre, a.apellidos
            FROM alumnos a
            INNER JOIN matriculas m ON m.id_alumno = a.id
            WHERE m.id_modulo = {$id_modulo}
        ");
    }

    function asignar_grupo($id_modulo, $id_grupo)
    {
        return $this->ejecutar_sql("
            UPDATE modulos
            SET id_grupo = {$id_grupo}
            WHERE id = {$id_modulo}
        ");
    }
}