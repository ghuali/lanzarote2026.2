<?php

if (Campo::val('modo') == 'ajax')
    define('BOTON_ENVIAR',"<button onclick=\"fetchJSON('/modulos/".Campo::val('oper')."/". Campo::val('id') ."?modo=ajax','formulario');return false\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");
else
    define('BOTON_ENVIAR',"<button type=\"submit\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");

class ModuloController
{
    static $id, $nombre, $codigo, $es_optativo, $grupo, $oper, $paso;

    static function inicializacion_campos()
    {
        Formulario::reset();

        self::$paso        = new Hidden(['nombre' => 'paso']);
        self::$oper        = new Hidden(['nombre' => 'oper']);
        self::$id          = new Hidden(['nombre' => 'id']);
        self::$nombre      = new Text(['nombre' => 'nombre']);
        self::$codigo      = new Text(['nombre' => 'codigo']);
        self::$es_optativo = new Checkbox(['nombre' => 'es_optativo']);

        $curso_modelo  = new Curso();
        $listado_grupos = $curso_modelo->cargar();
        self::$grupo   = new Select([
            'nombre'  => 'grupo',
            'options' => $listado_grupos
        ]);

        Formulario::cargar_elemento(self::$paso);
        Formulario::cargar_elemento(self::$oper);
        Formulario::cargar_elemento(self::$id);
        Formulario::cargar_elemento(self::$nombre);
        Formulario::cargar_elemento(self::$codigo);
        Formulario::cargar_elemento(self::$es_optativo);
        Formulario::cargar_elemento(self::$grupo);
    }

    static function pintar()
    {
        $contenido = '';
        self::inicializacion_campos();

        switch(Campo::val('oper'))
        {
            case 'cons':      $contenido = self::cons();                       break;
            case 'modi':      $contenido = self::modi();                       break;
            case 'baja':      $contenido = self::baja();                       break;
            case 'alta':      $contenido = self::alta();                       break;
            case 'profesores':$contenido = self::profesores(Campo::val('id')); break;
            case 'alumnos':   $contenido = self::alumnos(Campo::val('id'));    break;
            default:          $contenido = self::listado();                    break;
        }

        if (Campo::val('modo') != 'ajax')
            $h1cabecera = "<h1>". Idioma::lit('titulo'.Campo::val('oper'))." ". Idioma::lit(Campo::val('seccion')) ."</h1>";

        return "
        <div class=\"container contenido\">
        <section class=\"page-section modulos\" id=\"modulos\">
            {$h1cabecera}
            {$contenido}
        </section>
        </div>";
    }

    static function formulario($boton_enviar='',$errores=[],$mensaje_exito='',$disabled='')
    {
        Formulario::disabled($disabled);
        Campo::val('paso','1');
        return Formulario::pintar('/modulos/',$boton_enviar,$mensaje_exito);
    }

    static function sincro_form_bbdd($registro)
    {
        Formulario::sincro_form_bbdd($registro);

        if(!empty($registro['id']))
        {
            $modulo = new Modulo();
            Campo::val('grupo', $registro['id_grupo']);
        }
    }

    static function cons()
    {
        $modulo = new Modulo();
        $registro = $modulo->recuperar(Campo::val('id'));
        self::sincro_form_bbdd($registro);
        return self::formulario('',[],''," disabled=\"disabled\" ");
    }

    static function baja()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled = " disabled=\"disabled\" ";

        if(!Campo::val('paso'))
        {
            $modulo = new Modulo();
            $registro = $modulo->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $modulo = new Modulo();
            $modulo->actualizar(['fecha_baja' => date('Ymd')], Campo::val('id'));

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
            $modulo = new Modulo();
            $registro = $modulo->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $modulo = new Modulo();
                $modulo->actualizar([
                    'nombre'      => Campo::val('nombre'),
                    'codigo'      => Campo::val('codigo'),
                    'es_optativo' => Campo::val('es_optativo') ? 1 : 0
                ], Campo::val('id'));

                if(Campo::val('es_optativo'))
                    $modulo->asignar_grupo(Campo::val('id'), Campo::val('grupo'));

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
                $modulo = new Modulo();
                $id = $modulo->insertar([
                    'nombre'      => Campo::val('nombre'),
                    'codigo'      => Campo::val('codigo'),
                    'es_optativo' => Campo::val('es_optativo') ? 1 : 0
                ]);

                if(Campo::val('es_optativo'))
                    $modulo->asignar_grupo($id, Campo::val('grupo'));

                $mensaje_exito = '<p class="centrado alert alert-success">'. Idioma::lit('operacion_exito') .'</p>';
                $disabled = " disabled=\"disabled\" ";
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

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

        $modulo = new Modulo();
        $datos_consulta = $modulo->get_rows([
            'wheremayor' => ['fecha_baja' => date('Ymd')],
            'limit'      => LISTADO_TOTAL_POR_PAGINA,
            'offset'     => $offset
        ]);

        $filas = '';
        $total_registros = 0;

        foreach($datos_consulta as $registro)
        {
            $botonera = "
                <a onclick=\"fetchJSON('/modulos/cons/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-secondary\"><i class=\"bi bi-search\"></i></a>
                <a onclick=\"fetchJSON('/modulos/modi/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-primary\"><i class=\"bi bi-pencil-square\"></i></a>
                <a href=\"/modulos/baja/{$registro['id']}\" class=\"btn btn-danger\"><i class=\"bi bi-trash\"></i></a>
                <a href=\"/modulos/profesores/{$registro['id']}\" class=\"btn btn-info\"><i class=\"bi bi-person-badge\"></i></a>
                <a onclick=\"fetchJSON('/modulos/alumnos/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-warning\"><i class=\"bi bi-people\"></i></a>
            ";

            $filas .= "
                <tr>
                    <th style=\"white-space:nowrap\" scope=\"row\">{$botonera}</th>
                    <td>{$registro['nombre']}</td>
                    <td>{$registro['codigo']}</td>
                    <td>".($registro['es_optativo'] ? 'Sí' : 'No')."</td>
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
                    <th>Código</th>
                    <th>Optativo</th>
                </tr>
            </thead>
            <tbody>
                {$filas}
            </tbody>
            </table>
            {$barra_navegacion}
            <a href=\"/modulos/alta\" class=\"btn btn-primary\"><i class=\"bi bi-file-earmark-plus\"></i> Alta módulo</a>
        ";
    }

    // Solo lectura
    static function profesores($id_modulo)
    {
        $modulo    = new Modulo();
        $profesores = $modulo->get_profesores($id_modulo);

        $html = '<ul>';
        foreach($profesores as $p) $html .= "<li>{$p['nombre']} — {$p['email']}</li>";
        $html .= '</ul>';

        return $html;
    }

    // AJAX en modal
    static function alumnos($id_modulo)
    {
        $modulo  = new Modulo();
        $alumnos = $modulo->get_alumnos($id_modulo);

        $html = '<ul>';
        foreach($alumnos as $a) $html .= "<li>{$a['nombre']} {$a['apellidos']}</li>";
        $html .= '</ul>';

        return $html;
    }
}