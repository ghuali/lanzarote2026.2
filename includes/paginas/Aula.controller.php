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
            $h1cabecera = "<h1>". Idioma::lit('titulo'.Campo::val('oper'))." ". Idioma::lit(Campo::val('seccion')) ."</h1>";

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
                'Primera' => 'Primera',
                'Segunda' => 'Segunda',
                'Tercera' => 'Tercera'
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

    // ✅ Baja lógica como el profe
    static function baja()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled = " disabled=\"disabled\" ";

        if(!Campo::val('paso'))
        {
            $aula = new Aula();
            $registro = $aula->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $aula = new Aula();

            // ✅ Baja lógica en lugar de borrado físico
            $aula->actualizar(['fecha_baja' => date('Ymd')], Campo::val('id'));

            $mensaje_exito = '<p class="centrado alert alert-success">'. Idioma::lit('operacion_exito') .'</p>';
            $boton_enviar = '';
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    static function modi()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled = '';

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
                $aula->actualizar([
                    'nombre' => Campo::val('nombre'),
                    'letra'  => Campo::val('letra'),
                    'numero' => Campo::val('numero'),
                    'planta' => Campo::val('planta')
                ], Campo::val('id'));

                $mensaje_exito = '<p class="centrado alert alert-success">'. Idioma::lit('operacion_exito') .'</p>';
                $disabled = " disabled=\"disabled\" ";
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    static function alta()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled = '';

        if(Campo::val('paso'))
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $aula = new Aula();
                $aula->insertar([
                    'nombre' => Campo::val('nombre'),
                    'letra'  => Campo::val('letra'),
                    'numero' => Campo::val('numero'),
                    'planta' => Campo::val('planta')
                ]);

                $mensaje_exito = '<p class="centrado alert alert-success">'. Idioma::lit('operacion_exito') .'</p>';
                $disabled = " disabled=\"disabled\" ";
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    // ✅ Listado con paginación y filtro de activos como el profe
    static function listado()
    {
        if(is_numeric(Campo::val('pagina')))
        {
            $pagina = Campo::val('pagina');
            $offset = LISTADO_TOTAL_POR_PAGINA * $pagina;
        }
        else
        {
            $offset = '0';
        }
        $pagina++;

        $aula = new Aula();
        $datos_consulta = $aula->get_rows([
            'wheremayor' => ['fecha_baja' => date('Ymd')],
            'limit'      => LISTADO_TOTAL_POR_PAGINA,
            'offset'     => $offset
        ]);

        $filas = '';
        $total_registros = 0;

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

            $total_registros++;
        }

        $barra_navegacion = Template::navegacion($total_registros, $pagina);

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
            {$barra_navegacion}
            <a href=\"/aulas/alta\" class=\"btn btn-primary\"><i class=\"bi bi-file-earmark-plus\"></i> Alta aula</a>
        ";
    }
}