<?php

if (Campo::val('modo') == 'ajax')
    define('BOTON_ENVIAR',"<button onclick=\"fetchJSON('/tutores/".Campo::val('oper')."/". Campo::val('id') ."?modo=ajax','formulario');return false\" class=\"btn btn-primary\">Enviar</button>");
else
    define('BOTON_ENVIAR',"<button type=\"submit\" class=\"btn btn-primary\">Enviar</button>");

class TutorController
{
    static $id, $nombre, $email, $antiguedad, $oper, $paso;

    // Inicialización de campos
    static function inicializacion_campos()
    {
        Formulario::reset();

        self::$paso      = new Hidden(['nombre'=>'paso']);
        self::$oper      = new Hidden(['nombre'=>'oper']);
        self::$id        = new Hidden(['nombre'=>'id']);
        self::$nombre    = new Text(['nombre'=>'nombre']);
        self::$email     = new Text(['nombre'=>'email']);
        self::$antiguedad = new RadioButton([
            'nombre'=>'antiguedad',
            'options'=>[
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

    // Pintar la vista según operación
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
            case 'horario': $contenido = self::horario(Campo::val('id')); break;
            default:     $contenido = self::listado(); break;
        }

        $h1cabecera = "<h1>Gestión de Tutores</h1>";

        return "
        <div class=\"container contenido\">
        <section class=\"page-section tutores\" id=\"tutores\">
            {$h1cabecera}
            {$contenido}
        </section>
        </div>";
    }

    // Generar formulario
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

    // Consultar un tutor
    static function cons()
    {
        $tutor = new Tutor();
        $registro = $tutor->recuperar(Campo::val('id'));
        self::sincro_form_bbdd($registro);
        return self::formulario('',[],''," disabled=\"disabled\" ");
    }

    // Eliminar un tutor
    static function baja()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito = '';
        $disabled=" disabled=\"disabled\" ";

        if(!Campo::val('paso'))
        {
            $tutor = new Tutor();
            $registro = $tutor->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $tutor = new Tutor();
            $tutor->borrar(Campo::val('id'));

            $mensaje_exito = '<p class="alert alert-success">Tutor eliminado correctamente</p>';
            $boton_enviar = '';
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    // Modificar un tutor
    static function modi()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito='';

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
                $datos_actualizar = [
                    'nombre'=>Campo::val('nombre'),
                    'email'=>Campo::val('email'),
                    'antiguedad'=>Campo::val('antiguedad')
                ];
                $tutor->actualizar($datos_actualizar, Campo::val('id'));

                $mensaje_exito = '<p class="alert alert-success">Tutor modificado correctamente</p>';
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito);
    }

    // Crear un tutor
    static function alta()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito='';

        if(Campo::val('paso'))
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $tutor = new Tutor();
                $datos = [
                    'nombre'=>Campo::val('nombre'),
                    'email'=>Campo::val('email'),
                    'antiguedad'=>Campo::val('antiguedad')
                ];
                $tutor->insertar($datos);

                $mensaje_exito = '<p class="alert alert-success">Tutor creado correctamente</p>';
                $boton_enviar = '';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito);
    }

    // Listado de tutores
    static function listado()
    {
        $tutor = new Tutor();
        $datos = $tutor->get_rows();

        $filas = '';
        foreach($datos as $registro)
        {
            $botonera = "
                <a onclick=\"fetchJSON('/tutores/cons/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-secondary\">Ver</a>
                <a onclick=\"fetchJSON('/tutores/modi/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-primary\">Editar</a>
                <a href=\"/tutores/baja/{$registro['id']}\" class=\"btn btn-danger\">Borrar</a>
                <a href=\"/tutores/horario/{$registro['id']}\" class=\"btn btn-info\">Horario</a>
            ";

            $filas .= "
                <tr>
                    <th>{$botonera}</th>
                    <td>{$registro['nombre']}</td>
                    <td>{$registro['email']}</td>
                    <td>{$registro['antiguedad']}</td>
                </tr>
            ";
        }

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
            <a href=\"/tutores/alta\" class=\"btn btn-primary\">Alta Tutor</a>
        ";
    }

    // Mostrar horario de módulos del tutor
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
