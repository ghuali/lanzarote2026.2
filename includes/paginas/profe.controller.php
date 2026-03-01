<?php

if (Campo::val('modo') == 'ajax')
    define('BOTON_ENVIAR',"<button onclick=\"fetchJSON('/profesores/".Campo::val('oper')."/". Campo::val('id') ."?modo=ajax','formulario');return false\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");
else
    define('BOTON_ENVIAR',"<button type=\"submit\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");

class ProfesorController
{
    static $id, $nombre, $email, $es_tutor, $cursos, $oper, $paso;

    static function inicializacion_campos()
    {
        Formulario::reset();

        self::$paso     = new Hidden(['nombre'=>'paso']);
        self::$oper     = new Hidden(['nombre'=>'oper']);
        self::$id       = new Hidden(['nombre'=>'id']);
        self::$nombre   = new Text(['nombre'=>'nombre']);
        self::$email    = new Text(['nombre'=>'email']);
        self::$es_tutor = new Checkbox(['nombre'=>'es_tutor']);

        $curso_modelo   = new Curso();
        $listado_cursos = $curso_modelo->cargar();
        self::$cursos   = new Select([
            'nombre'  => 'cursos',
            'options' => $listado_cursos
        ]);

        Formulario::cargar_elemento(self::$paso);
        Formulario::cargar_elemento(self::$oper);
        Formulario::cargar_elemento(self::$id);
        Formulario::cargar_elemento(self::$nombre);
        Formulario::cargar_elemento(self::$email);
        Formulario::cargar_elemento(self::$es_tutor);
        Formulario::cargar_elemento(self::$cursos);
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
            case 'alumnos': $contenido = self::alumnos(Campo::val('id')); break;
            case 'horario': $contenido = self::horario(Campo::val('id')); break;
            default:        $contenido = self::listado();                 break;
        }

        // ✅ Solo pintar h1 fuera de ajax
        if (Campo::val('modo') != 'ajax')
            $h1cabecera = "<h1>". Idioma::lit('titulo'.Campo::val('oper'))." ". Idioma::lit(Campo::val('seccion')) ."</h1>";

        return "
        <div class=\"container contenido\">
        <section class=\"page-section profesores\" id=\"profesores\">
            {$h1cabecera}
            {$contenido}
        </section>
        </div>";
    }

    static function formulario($boton_enviar='',$errores=[],$mensaje_exito='',$disabled='')
    {
        Formulario::disabled($disabled);
        Campo::val('paso','1');
        return Formulario::pintar('/profesores/',$boton_enviar,$mensaje_exito);
    }

    static function sincro_form_bbdd($registro)
    {
        Formulario::sincro_form_bbdd($registro);

        if(!empty($registro['id']))
        {
            $profesor = new Profesor();
            $tutorias = $profesor->get_cursos($registro['id']);
            if($tutorias)
                Campo::val('cursos', $tutorias[0]);
        }
    }

    static function cons()
    {
        $profesor = new Profesor();
        $registro = $profesor->recuperar(Campo::val('id'));
        self::sincro_form_bbdd($registro);
        return self::formulario('',[],''," disabled=\"disabled\" ");
    }

    // ✅ Baja lógica
    static function baja()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled = " disabled=\"disabled\" ";

        if(!Campo::val('paso'))
        {
            $profesor = new Profesor();
            $registro = $profesor->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $profesor = new Profesor();
            $profesor->actualizar(['fecha_baja' => date('Ymd')], Campo::val('id'));

            $mensaje_exito = '<p class="centrado alert alert-success">'. Idioma::lit('operacion_exito') .'</p>';
            $boton_enviar = '';
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    // ✅ Deshabilitar tras éxito
    static function modi()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled = '';

        if(!Campo::val('paso'))
        {
            $profesor = new Profesor();
            $registro = $profesor->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $profesor = new Profesor();
                $profesor->actualizar([
                    'nombre'   => Campo::val('nombre'),
                    'email'    => Campo::val('email'),
                    'es_tutor' => Campo::val('es_tutor') ? 1 : 0
                ], Campo::val('id'));

                if(Campo::val('es_tutor'))
                    $profesor->asignar_curso(Campo::val('id'), Campo::val('cursos'));

                $mensaje_exito = '<p class="centrado alert alert-success">'. Idioma::lit('operacion_exito') .'</p>';
                $disabled = " disabled=\"disabled\" ";
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    // ✅ Deshabilitar tras éxito
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
                $profesor = new Profesor();
                $id = $profesor->insertar([
                    'nombre'   => Campo::val('nombre'),
                    'email'    => Campo::val('email'),
                    'es_tutor' => Campo::val('es_tutor') ? 1 : 0
                ]);

                if(Campo::val('es_tutor'))
                    $profesor->asignar_curso($id, Campo::val('cursos'));

                $mensaje_exito = '<p class="centrado alert alert-success">'. Idioma::lit('operacion_exito') .'</p>';
                $disabled = " disabled=\"disabled\" ";
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    // ✅ Paginación y filtro de activos
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

        $profesor = new Profesor();
        $datos = $profesor->get_rows([
            'wheremayor' => ['fecha_baja' => date('Ymd')],
            'limit'      => LISTADO_TOTAL_POR_PAGINA,
            'offset'     => $offset
        ]);

        $filas = '';
        $total_registros = 0;

        foreach($datos as $registro)
        {
            $botonera = "
                <a onclick=\"fetchJSON('/profesores/cons/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-secondary\"><i class=\"bi bi-search\"></i></a>
                <a onclick=\"fetchJSON('/profesores/modi/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-primary\"><i class=\"bi bi-pencil-square\"></i></a>
                <a href=\"/profesores/baja/{$registro['id']}\" class=\"btn btn-danger\"><i class=\"bi bi-trash\"></i></a>
                <a href=\"/profesores/alumnos/{$registro['id']}\" class=\"btn btn-info\"><i class=\"bi bi-people\"></i></a>
                <a href=\"/profesores/horario/{$registro['id']}\" class=\"btn btn-warning\"><i class=\"bi bi-calendar\"></i></a>
            ";

            $filas .= "
                <tr>
                    <th style=\"white-space:nowrap\" scope=\"row\">{$botonera}</th>
                    <td>{$registro['nombre']}</td>
                    <td>{$registro['email']}</td>
                    <td>".($registro['es_tutor'] ? 'Sí' : 'No')."</td>
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
                        <th>Email</th>
                        <th>Tutor</th>
                    </tr>
                </thead>
                <tbody>{$filas}</tbody>
            </table>
            {$barra_navegacion}
            <a href=\"/profesores/alta\" class=\"btn btn-primary\"><i class=\"bi bi-file-earmark-plus\"></i> Alta profesor</a>
        ";
    }

    static function alumnos($id_profesor)
    {
        $profesor = new Profesor();
        $alumnos  = $profesor->get_alumnos($id_profesor);

        $html = '<ul>';
        foreach($alumnos as $a) $html .= "<li>{$a['nombre']} {$a['apellidos']}</li>";
        $html .= '</ul>';

        return $html;
    }

    static function horario($id_profesor)
    {
        $profesor = new Profesor();
        $modulos  = $profesor->get_horario($id_profesor);

        $html = '<ul>';
        foreach($modulos as $m) $html .= "<li>{$m['nombre_modulo']}</li>";
        $html .= '</ul>';

        return $html;
    }
}