<?php

if (Campo::val('modo') == 'ajax')
    define('BOTON_ENVIAR',"<button onclick=\"fetchJSON('/tutores/".Campo::val('oper')."/". Campo::val('id') ."?modo=ajax','formulario');return false\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");
else
    define('BOTON_ENVIAR',"<button type=\"submit\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");

class TutorController
{
    static $id, $nombre, $email, $antiguedad, $oper, $paso;

    static function inicializacion_campos()
    {
        Formulario::reset();

        self::$paso       = new Hidden(['nombre'=>'paso']);
        self::$oper       = new Hidden(['nombre'=>'oper']);
        self::$id         = new Hidden(['nombre'=>'id']);
        self::$nombre     = new Text(['nombre'=>'nombre']);
        self::$email      = new Text(['nombre'=>'email']);
        self::$antiguedad = new RadioButton([
            'nombre'  => 'antiguedad',
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
        Formulario::cargar_elemento(self::$email);
        Formulario::cargar_elemento(self::$antiguedad);
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
            case 'horario': $contenido = self::horario(Campo::val('id')); break;
            default:        $contenido = self::listado();                 break;
        }

        // ✅ Solo fuera de ajax
        if (Campo::val('modo') != 'ajax')
            $h1cabecera = "<h1>". Idioma::lit('titulo'.Campo::val('oper'))." ". Idioma::lit(Campo::val('seccion')) ."</h1>";

        return "
        <div class=\"container contenido\">
        <section class=\"page-section tutores\" id=\"tutores\">
            {$h1cabecera}
            {$contenido}
        </section>
        </div>";
    }

    static function formulario($boton_enviar='',$errores=[],$mensaje_exito='',$disabled='')
    {
        Formulario::disabled($disabled);
        Campo::val('paso','1');
        return Formulario::pintar('/tutores/',$boton_enviar,$mensaje_exito);
    }

    static function sincro_form_bbdd($registro)
    {
        Formulario::sincro_form_bbdd($registro);
    }

    static function cons()
    {
        $tutor = new Tutor();
        $registro = $tutor->recuperar(Campo::val('id'));
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
            $tutor = new Tutor();
            $registro = $tutor->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $tutor = new Tutor();
            $tutor->actualizar(['fecha_baja' => date('Ymd')], Campo::val('id'));

            $mensaje_exito = '<p class="centrado alert alert-success">'. Idioma::lit('operacion_exito') .'</p>';
            $boton_enviar = '';
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    // ✅ Deshabilitar tras éxito + disabled correcto
    static function modi()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled = '';

        if(!Campo::val('paso'))
        {
            $tutor = new Tutor();
            $registro = $tutor->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $tutor = new Tutor();
                $tutor->actualizar([
                    'nombre'    => Campo::val('nombre'),
                    'email'     => Campo::val('email'),
                    'antiguedad'=> Campo::val('antiguedad')
                ], Campo::val('id'));

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
                $tutor = new Tutor();
                $tutor->insertar([
                    'nombre'    => Campo::val('nombre'),
                    'email'     => Campo::val('email'),
                    'antiguedad'=> Campo::val('antiguedad')
                ]);

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

        $tutor = new Tutor();
        $datos = $tutor->get_rows([
            'wheremayor' => ['fecha_baja' => date('Ymd')],
            'limit'      => LISTADO_TOTAL_POR_PAGINA,
            'offset'     => $offset
        ]);

        $filas = '';
        $total_registros = 0;

        foreach($datos as $registro)
        {
            $botonera = "
                <a onclick=\"fetchJSON('/tutores/cons/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-secondary\"><i class=\"bi bi-search\"></i></a>
                <a onclick=\"fetchJSON('/tutores/modi/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-primary\"><i class=\"bi bi-pencil-square\"></i></a>
                <a href=\"/tutores/baja/{$registro['id']}\" class=\"btn btn-danger\"><i class=\"bi bi-trash\"></i></a>
                <a href=\"/tutores/horario/{$registro['id']}\" class=\"btn btn-info\"><i class=\"bi bi-calendar\"></i></a>
            ";

            $filas .= "
                <tr>
                    <th style=\"white-space:nowrap\" scope=\"row\">{$botonera}</th>
                    <td>{$registro['nombre']}</td>
                    <td>{$registro['email']}</td>
                    <td>{$registro['antiguedad']}</td>
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
                        <th>Antigüedad</th>
                    </tr>
                </thead>
                <tbody>
                    {$filas}
                </tbody>
            </table>
            {$barra_navegacion}
            <a href=\"/tutores/alta\" class=\"btn btn-primary\"><i class=\"bi bi-file-earmark-plus\"></i> Alta tutor</a>
        ";
    }

    static function horario($id_tutor)
    {
        $tutor = new Tutor();
        $modulos = $tutor->get_horario($id_tutor);

        $tabla = '<ul>';
        foreach($modulos as $m) $tabla .= "<li>{$m['nombre_modulo']}</li>";
        $tabla .= '</ul>';

        return $tabla;
    }
}