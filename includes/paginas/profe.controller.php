<?php

if (Campo::val('modo') == 'ajax')
    define('BOTON_ENVIAR',"<button onclick=\"fetchJSON('/profesores/".Campo::val('oper')."/". Campo::val('id') ."?modo=ajax','formulario');return false\" class=\"btn btn-primary\">Enviar</button>");
else
    define('BOTON_ENVIAR',"<button type=\"submit\" class=\"btn btn-primary\">Enviar</button>");

class ProfesorController
{
    static $id, $nombre, $email, $es_tutor, $cursos, $oper, $paso;

    // Inicialización de campos
    static function inicializacion_campos()
    {
        Formulario::reset();

        self::$paso     = new Hidden(['nombre'=>'paso']);
        self::$oper     = new Hidden(['nombre'=>'oper']);
        self::$id       = new Hidden(['nombre'=>'id']);
        self::$nombre   = new Text(['nombre'=>'nombre']);
        self::$email    = new Text(['nombre'=>'email']);
        self::$es_tutor = new Checkbox(['nombre'=>'es_tutor']);
        
        // Cursos solo se muestran si es tutor
        $curso_modelo = new Curso();
        $listado_cursos = $curso_modelo->cargar(); // array id => nombre
        self::$cursos = new Select([
            'nombre'=>'cursos',
            'options'=>$listado_cursos
        ]);

        Formulario::cargar_elemento(self::$paso);
        Formulario::cargar_elemento(self::$oper);
        Formulario::cargar_elemento(self::$id);
        Formulario::cargar_elemento(self::$nombre);
        Formulario::cargar_elemento(self::$email);
        Formulario::cargar_elemento(self::$es_tutor);
        Formulario::cargar_elemento(self::$cursos);
    }

    // Pintar formulario según operación
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
            case 'alumnos': $contenido = self::alumnos(Campo::val('id')); break;
            case 'horario': $contenido = self::horario(Campo::val('id')); break;
            default: $contenido = self::listado(); break;
        }

        $h1cabecera = "<h1>Gestión de Profesores</h1>";

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

        // Si es tutor, seleccionamos los cursos asignados
        if(!empty($registro['id_profesor']))
        {
            $profesor = new Profesor();
            $tutorias = $profesor->get_cursos($registro['id_profesor']); // array de id_curso
            if($tutorias)
            {
                Campo::val('cursos',$tutorias[0]); // selecciona el primero (ajustable a multi-select)
            }
        }
    }

    // Consultar
    static function cons()
    {
        $profesor = new Profesor();
        $registro = $profesor->recuperar(Campo::val('id'));
        self::sincro_form_bbdd($registro);
        return self::formulario('',[],''," disabled=\"disabled\" ");
    }

    // Eliminar
    static function baja()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito='';
        $disabled=" disabled=\"disabled\" ";

        if(!Campo::val('paso'))
        {
            $profesor = new Profesor();
            $registro = $profesor->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $profesor = new Profesor();
            $profesor->borrar(Campo::val('id'));
            $mensaje_exito = '<p class="alert alert-success">Profesor eliminado correctamente</p>';
            $boton_enviar='';
        }

        return self::formulario($boton_enviar,[],$mensaje_exito,$disabled);
    }

    // Modificar
    static function modi()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito='';

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
                $datos = [
                    'nombre'=>Campo::val('nombre'),
                    'email'=>Campo::val('email'),
                    'es_tutor'=>Campo::val('es_tutor') ? 1 : 0
                ];
                $profesor->actualizar($datos, Campo::val('id'));

                // Guardamos cursos si es tutor
                if(Campo::val('es_tutor'))
                {
                    $profesor->asignar_curso(Campo::val('id'), Campo::val('cursos'));
                }

                $mensaje_exito = '<p class="alert alert-success">Profesor modificado correctamente</p>';
                $boton_enviar='';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito);
    }

    // Crear
    static function alta()
    {
        $boton_enviar = BOTON_ENVIAR;
        $mensaje_exito='';

        if(Campo::val('paso'))
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $profesor = new Profesor();
                $datos = [
                    'nombre'=>Campo::val('nombre'),
                    'email'=>Campo::val('email'),
                    'es_tutor'=>Campo::val('es_tutor') ? 1 : 0
                ];
                $id = $profesor->insertar($datos);

                if(Campo::val('es_tutor'))
                {
                    $profesor->asignar_curso($id, Campo::val('cursos'));
                }

                $mensaje_exito = '<p class="alert alert-success">Profesor creado correctamente</p>';
                $boton_enviar='';
            }
        }

        return self::formulario($boton_enviar,[],$mensaje_exito);
    }

    // Listado
    static function listado()
    {
        $profesor = new Profesor();
        $datos = $profesor->get_rows();

        $filas = '';
        foreach($datos as $registro)
        {
            $botonera = "
                <a onclick=\"fetchJSON('/profesores/cons/{$registro['id_profesor']}?modo=ajax')\" class=\"btn btn-secondary\">Ver</a>
                <a onclick=\"fetchJSON('/profesores/modi/{$registro['id_profesor']}?modo=ajax')\" class=\"btn btn-primary\">Editar</a>
                <a href=\"/profesores/baja/{$registro['id_profesor']}\" class=\"btn btn-danger\">Borrar</a>
                <a href=\"/profesores/alumnos/{$registro['id_profesor']}\" class=\"btn btn-info\">Alumnos</a>
                <a href=\"/profesores/horario/{$registro['id_profesor']}\" class=\"btn btn-warning\">Horario</a>
            ";
            $filas .= "
                <tr>
                    <th>{$botonera}</th>
                    <td>{$registro['nombre']}</td>
                    <td>{$registro['email']}</td>
                    <td>".($registro['es_tutor'] ? 'Sí':'No')."</td>
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
                        <th>Tutor</th>
                    </tr>
                </thead>
                <tbody>{$filas}</tbody>
            </table>
            <a href=\"/profesores/alta\" class=\"btn btn-primary\">Alta Profesor</a>
        ";
    }

    // Listado de alumnos de los cursos del tutor
    static function alumnos($id_profesor)
    {
        $profesor = new Profesor();
        $alumnos = $profesor->get_alumnos($id_profesor); // array de alumnos

        $html = '<ul>';
        foreach($alumnos as $a) $html .= "<li>{$a['nombre']} {$a['apellidos']}</li>";
        $html .= '</ul>';

        return $html;
    }

    // Horario AJAX
    static function horario($id_profesor)
    {
        $profesor = new Profesor();
        $modulos = $profesor->get_horario($id_profesor);

        $html = '<ul>';
        foreach($modulos as $m) $html .= "<li>{$m['nombre_modulo']}</li>";
        $html .= '</ul>';

        return $html;
    }
}
