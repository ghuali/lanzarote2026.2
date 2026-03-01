<?php

if (Campo::val('modo') == 'ajax')
    define('BOTON_ENVIAR',"<button onclick=\"fetchJSON('/alumnos/".Campo::val('oper')."/". Campo::val('id') ."?modo=ajax','formulario');return false\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");
else
    define('BOTON_ENVIAR',"<button type=\"submit\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");

class AlumnoController
{
    static $id, $nombre, $apellidos, $año_matricula, $oper, $paso;

    static function inicializacion_campos()
    {
        self::$paso           = new Hidden(['nombre' => 'paso']);
        self::$oper           = new Hidden(['nombre' => 'oper']);
        self::$id             = new Hidden(['nombre' => 'id']);
        self::$nombre         = new Text(['nombre' => 'nombre']);
        self::$apellidos      = new Text(['nombre' => 'apellidos']);
        self::$año_matricula  = new RadioButton([
            'nombre'  => 'año_matricula',
            'options' => [
                '2018'=>'2018','2019'=>'2019','2020'=>'2020',
                '2021'=>'2021','2022'=>'2022','2023'=>'2023',
                '2024'=>'2024','2025'=>'2025'
            ]
        ]);

        Formulario::cargar_elemento(self::$paso);
        Formulario::cargar_elemento(self::$oper);
        Formulario::cargar_elemento(self::$id);
        Formulario::cargar_elemento(self::$nombre);
        Formulario::cargar_elemento(self::$apellidos);
        Formulario::cargar_elemento(self::$año_matricula);
    }

    static function pintar()
    {
        $contenido = '';
        self::inicializacion_campos();

        switch(Campo::val('oper'))
        {
            case 'cons':    $contenido = self::cons();                    break;
            case 'modi':    $contenido = self::modi();                    break;
            case 'baja':    $contenido = self::baja();                    break;
            case 'alta':    $contenido = self::alta();                    break;
            case 'modulos': $contenido = self::modulos(Campo::val('id')); break;
            default:        $contenido = self::listado();                 break;
        }

        if (Campo::val('modo') != 'ajax')
            $h1cabecera = "<h1>". Idioma::lit('titulo'.Campo::val('oper'))." ". Idioma::lit(Campo::val('seccion')) ."</h1>";

        return "
        <div class=\"container contenido\">
        <section class=\"page-section alumnos\" id=\"alumnos\">
            {$h1cabecera}
            {$contenido}
        </section>
        </div>";
    }

    static function formulario($boton_enviar='',$errores=[],$mensaje_exito='',$disabled='')
    {
        Formulario::disabled($disabled);
        Campo::val('paso','1');
        return Formulario::pintar('/alumnos/',$boton_enviar,$mensaje_exito);
    }

    static function sincro_form_bbdd($registro)
    {
        Formulario::sincro_form_bbdd($registro);
    }

    static function cons()
    {
        $alumno = new Alumno();
        $registro = $alumno->recuperar(Campo::val('id'));
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
            $alumno = new Alumno();
            $registro = $alumno->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $alumno = new Alumno();
            $alumno->actualizar(['fecha_baja' => date('Ymd')], Campo::val('id'));

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
            $alumno = new Alumno();
            $registro = $alumno->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $alumno = new Alumno();
                $alumno->actualizar([
                    'nombre'        => Campo::val('nombre'),
                    'apellidos'     => Campo::val('apellidos'),
                    'año_matricula' => Campo::val('año_matricula')
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
                $alumno = new Alumno();
                $alumno->insertar([
                    'nombre'        => Campo::val('nombre'),
                    'apellidos'     => Campo::val('apellidos'),
                    'año_matricula' => Campo::val('año_matricula')
                ]);

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

        $alumno = new Alumno();
        $datos_consulta = $alumno->get_rows([
            'wheremayor' => ['fecha_baja' => date('Ymd')],
            'limit'      => LISTADO_TOTAL_POR_PAGINA,
            'offset'     => $offset
        ]);

        $filas = '';
        $total_registros = 0;

        foreach($datos_consulta as $registro)
        {
            $botonera = "
                <a onclick=\"fetchJSON('/alumnos/cons/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-secondary\"><i class=\"bi bi-search\"></i></a>
                <a onclick=\"fetchJSON('/alumnos/modi/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-primary\"><i class=\"bi bi-pencil-square\"></i></a>
                <a href=\"/alumnos/baja/{$registro['id']}\" class=\"btn btn-danger\"><i class=\"bi bi-trash\"></i></a>
                <a href=\"/alumnos/modulos/{$registro['id']}\" class=\"btn btn-info\"><i class=\"bi bi-book\"></i></a>
            ";

            $filas .= "
                <tr>
                    <th style=\"white-space:nowrap\" scope=\"row\">{$botonera}</th>
                    <td>{$registro['nombre']}</td>
                    <td>{$registro['apellidos']}</td>
                    <td>{$registro['año_matricula']}</td>
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
                    <th>Apellidos</th>
                    <th>Año matriculación</th>
                </tr>
            </thead>
            <tbody>
                {$filas}
            </tbody>
            </table>
            {$barra_navegacion}
            <a href=\"/alumnos/alta\" class=\"btn btn-primary\"><i class=\"bi bi-file-earmark-plus\"></i> Alta alumno</a>
        ";
    }

    static function modulos($id_alumno)
    {
        $alumno  = new Alumno();
        $modulos = $alumno->get_modulos($id_alumno);

        $html = '<ul>';
        foreach($modulos as $m) $html .= "<li>{$m['nombre_modulo']}</li>";
        $html .= '</ul>';

        return $html;
    }
}