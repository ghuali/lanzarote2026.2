<?php

class Horario extends Base
{
    function __construct()
    {
        $this->tabla = 'horarios';
    }

    /**
     * Devuelve el horario con todos los datos: módulo, curso, letra, profesor, aula, color.
     */
    function obtenerHorarioCompleto()
    {
        $sql = "
            SELECT 
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                m.nombre AS nombre_modulo,
                m.siglas,
                m.color,
                c.nombre_grado AS curso,
                c.curso_numero,
                c.letra,
                CONCAT(p.nombre, ' ', p.apellidos) AS profesor,
                a.nombre AS aula
            FROM horarios h
            INNER JOIN modulos m ON h.id_modulo = m.id
            INNER JOIN cursos c ON m.curso_asignado = c.id
            INNER JOIN personas p ON h.id_profesor = p.id
            LEFT JOIN aulas a ON h.id_aula = a.id
            ORDER BY 
                FIELD(h.dia,'L','M','X','J','V'),
                h.hora_inicio ASC
        ";

        $query = new Query($sql);

        $resultado = [];
        while ($registro = $query->recuperar()) {
            $resultado[] = $registro;
        }

        return $resultado;
    }
}
