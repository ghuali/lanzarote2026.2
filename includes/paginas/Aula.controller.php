<?php

if (Campo::val('modo') == 'ajax')
    define('BOTON_ENVIAR',"<button onclick=\"fetchJSON('/aulas/".Campo::val('oper')."/". Campo::val('id') ."?modo=ajax','formulario');return false\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");
else
    define('BOTON_ENVIAR',"<button type=\"submit\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");

class AulaController
{
    static $id, $nombre, $letra, $numero, $planta, $oper, $paso;

    static function pintar()
    {
        $contenido = '';

        self::inicializacion_campos();

        switch(Campo::val('oper'))
        {
            case 'cons': $contenido = self::cons(); break;
            case 'modi': $contenido = self::modi(); break;
            case 'baja': $contenido = self::baja(); break;
            case 'alta': $contenido = self::alta(); break;
            default:     $contenido = self::listado(); break;
        }

        if (Campo::val('modo') != 'ajax')
            $h1cabecera = "<h1>". Idioma::lit('titulo'.Campo::val('oper'))." Aula</h1>";

        return "
        <div class=\"container contenido\">
        <section class=\"page-section aulas\" id=\"aulas\">
            {$h1cabecera}
            {$contenido}
        </section>
        </div>";
    }

    static function inicializacion_campos()
    {
        self::$paso   = new Hidden(['nombre' => 'paso']);
        self::$oper   = new Hidden(['nombre' => 'oper']);
        self::$id     = new Hidden(['nombre' => 'id']);
        self::$nombre = new Text(['nombre' => 'nombre']);
        self::$letra  = new Select(['nombre' => 'letra', 'options' => ['D'=>'Derecha','I'=>'Izquierda']]);
        self::$numero = new Number(['nombre' => 'numero']);
        self::$planta = new Select([
    'nombre' => 'planta',
    'options' => [
        'Primera'  => 'Primera',
        'Segunda'  => 'Segunda',
        'Tercera'  => 'Tercera'
    ]
]);
        Formulario::cargar_elemento(self::$paso);
        Formulario::cargar_elemento(self::$oper);
        Formulario::cargar_elemento(self::$id);
        Formulario::cargar_elemento(self::$nombre);
        Formulario::cargar_elemento(self::$letra);
        Formulario::cargar_elemento(self::$numero);
        Formulario::cargar_elemento(self::$planta);
    }

    static function formulario($boton_enviar='',$errores=[],$mensaje_exito='',$disabled='')
    {
        Formulario::disabled($disabled);
        Campo::val('paso','1');
        return Formulario::pintar('/aulas/',$boton_enviar,$mensaje_exito);
    }

    static function sincro_form_bbdd($registro)
    {
        Formulario::sincro_form_bbdd($registro);
    }

    static function cons()
    {
        $aula = new Aula();
        $registro = $aula->recuperar(Campo::val('id'));

        self::sincro_form_bbdd($registro);

        return self::formulario('',[],''," disabled=\"disabled\" ");
    }

    static function baja()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled=" disabled=\"disabled\" ";

        if(!Campo::val('paso'))
        {
            $aula = new Aula();
            $registro = $aula->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $aula = new Aula();
            $aula->borrar(Campo::val('id'));

            $mensaje_exito = '<p class="centrado alert alert-success">Aula eliminada con éxito</p>';
            $boton_enviar = '';
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    static function modi()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito='';
        $disabled='';

        if(!Campo::val('paso'))
        {
            $aula = new Aula();
            $registro = $aula->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $aula = new Aula();

                $datos_actualizar = [
                    'nombre' => Campo::val('nombre'),
                    'letra'  => Campo::val('letra'),
                    'numero' => Campo::val('numero'),
                    'planta' => Campo::val('planta')
                ];

                $aula->actualizar($datos_actualizar,Campo::val('id'));

                $mensaje_exito = '<p class="centrado alert alert-success">Aula modificada correctamente</p>';
                $disabled =" disabled=\"disabled\" ";
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    static function alta()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito='';
        $disabled='';

        if(Campo::val('paso'))
        {
            $errores = Formulario::validacion();

            if(!$errores)
            {
                $nueva = [
                    'nombre'=>Campo::val('nombre'),
                    'letra'=>Campo::val('letra'),
                    'numero'=>Campo::val('numero'),
                    'planta'=>Campo::val('planta')
                ];

                $aula = new Aula();
                $aula->insertar($nueva);

                $mensaje_exito = '<p class="centrado alert alert-success">Aula creada correctamente</p>';
                $disabled =" disabled=\"disabled\" ";
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    static function listado()
    {
        $aula = new Aula();
        $datos_consulta = $aula->get_rows();

        $filas = '';

        foreach($datos_consulta as $registro)
        {
            $botonera = "
                <a onclick=\"fetchJSON('/aulas/cons/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-secondary\"><i class=\"bi bi-search\"></i></a>
                <a onclick=\"fetchJSON('/aulas/modi/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-primary\"><i class=\"bi bi-pencil-square\"></i></a>
                <a href=\"/aulas/baja/{$registro['id']}\" class=\"btn btn-danger\"><i class=\"bi bi-trash\"></i></a>
            ";

            $filas .= "
                <tr>
                    <th style=\"white-space:nowrap\" scope=\"row\">{$botonera}</th>
                    <td>{$registro['nombre']}</td>
                    <td>{$registro['letra']}</td>
                    <td>{$registro['numero']}</td>
                    <td>{$registro['planta']}</td>
                </tr>
            ";
        }

        return "
            <table class=\"table\">
            <thead>
                <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Letra</th>
                <th>Número</th>
                <th>Planta</th>
                </tr>
            </thead>
            <tbody>
                {$filas}
            </tbody>
            </table>
            <a href=\"/aulas/alta\" class=\"btn btn-primary\"><i class=\"bi bi-file-earmark-plus\"></i> Alta aula</a>
        ";
    }
}
