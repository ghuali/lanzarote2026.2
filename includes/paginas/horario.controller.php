<?php

class HorarioController
{
    static $oper, $id, $paso;

    static function pintar()
    {
        $contenido = '';

        self::inicializacion_campos();

        switch(Campo::val('oper'))
        {
            case 'cons':
                $contenido = self::cons();
            break;
            default:
                $contenido = self::listado();
                $volver = '';
            break;
        }

        $h1cabecera = '';
        if (Campo::val('modo') != 'ajax')
        {
            $h1cabecera = "<h1>Horario del Centro</h1>";
        }

        return "
        <div class=\"container contenido\">
        <section class=\"page-section horario\" id=\"horario\">
            {$h1cabecera}
            {$contenido}
        </section>
        </div>
        ";
    }

    static function inicializacion_campos()
    {
        self::$paso = new Hidden(['nombre' => 'paso']);
        self::$oper = new Hidden(['nombre' => 'oper']);
        self::$id   = new Hidden(['nombre' => 'id']);

        Formulario::cargar_elemento(self::$paso);
        Formulario::cargar_elemento(self::$oper);
        Formulario::cargar_elemento(self::$id);
    }

    static function listado()
{
    $horario = new Horario();
    $datos = $horario->obtenerHorarioCompleto();

    $listado = '';
    foreach($datos as $fila) {
        $listado .= "
            <tr>
                <td>{$fila['hora']}</td>
                <td>{$fila['dia']}</td>
                <td>{$fila['nombre_modulo']}</td>
                <td>{$fila['siglas']}</td>
                <td>{$fila['curso']}</td>
                <td>{$fila['letra']}</td>
                <td>{$fila['profesor']}</td>
                <td>{$fila['color']}</td>
            </tr>
        ";
    }

    return "
        <table class='table'>
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Día</th>
                    <th>Módulo</th>
                    <th>Siglas</th>
                    <th>Curso</th>
                    <th>Letra</th>
                    <th>Profesor</th>
                    <th>Color</th>
                </tr>
            </thead>
            <tbody>
                {$listado}
            </tbody>
        </table>
    ";
}


    static function cons()
    {
        // Para mostrar detalles de una hora concreta, si quieres.
        $horario = new Horario();
        $registro = $horario->recuperar(Campo::val('id'));

        // Solo ejemplo: mostrar registro completo
        return "<pre>" . print_r($registro,true) . "</pre>";
    }
}
