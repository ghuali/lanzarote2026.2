<?php

class Aula extends Base
{
    function __construct()
    {
        $this->tabla = 'aulas';
    }

    function cargar()
    {
        $datos = [];
        $datos['select'] = 'id, nombre, letra, numero, planta';

        // Obtiene las filas usando la función heredada de Base
        $datos_consulta = $this->get_rows($datos);

        $aulas = [];

        foreach ($datos_consulta as $registro) {

            // Formato final para mostrar en dropdowns, listados, etc.
            // Ejemplo: "101 - 1DAW (D, Primera)"
            $aulas[$registro['id']] =
                $registro['numero'] . ' - ' .
                $registro['nombre'] .
                ' (' . $registro['letra'] . ', ' . $registro['planta'] . ')';
        }

        return $aulas;
    }
}
