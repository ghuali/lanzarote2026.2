<?php

if (Campo::val('modo') == 'ajax')
    define('BOTON_ENVIAR',"<button onclick=\"fetchJSON('/equipos/".Campo::val('oper')."/". Campo::val('id') ."?modo=ajax','formulario');return false\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");
else
    define('BOTON_ENVIAR',"<button type=\"submit\" class=\"btn btn-primary\">". Idioma::lit('enviar'.Campo::val('oper'))."</button>");

class EquipoController
{
    static $id, $nombre, $tipo, $numero_serie, $estado, $oper, $paso;

    static function inicializacion_campos()
    {
        self::$paso         = new Hidden(['nombre' => 'paso']);
        self::$oper         = new Hidden(['nombre' => 'oper']);
        self::$id           = new Hidden(['nombre' => 'id']);
        self::$nombre       = new Text(['nombre' => 'nombre']);
        self::$tipo         = new Select([
            'nombre'  => 'tipo',
            'options' => ['S' => 'Sobremesa', 'P' => 'Portátil']
        ]);
        self::$numero_serie = new Number(['nombre' => 'numero_serie']);
        self::$estado       = new Select([
            'nombre'  => 'estado',
            'options' => [
                'Operativo'      => 'Operativo',
                'En reparación'  => 'En reparación',
                'Baja'           => 'Baja'
            ]
        ]);

        Formulario::cargar_elemento(self::$paso);
        Formulario::cargar_elemento(self::$oper);
        Formulario::cargar_elemento(self::$id);
        Formulario::cargar_elemento(self::$nombre);
        Formulario::cargar_elemento(self::$tipo);
        Formulario::cargar_elemento(self::$numero_serie);
        Formulario::cargar_elemento(self::$estado);
    }

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
        <section class=\"page-section equipos\" id=\"equipos\">
            {$h1cabecera}
            {$contenido}
        </section>
        </div>";
    }

    static function formulario($boton_enviar='',$errores=[],$mensaje_exito='',$disabled='')
    {
        Formulario::disabled($disabled);
        Campo::val('paso','1');
        return Formulario::pintar('/equipos/',$boton_enviar,$mensaje_exito);
    }

    static function sincro_form_bbdd($registro)
    {
        Formulario::sincro_form_bbdd($registro);
    }

    static function cons()
    {
        $equipo = new Equipo();
        $registro = $equipo->recuperar(Campo::val('id'));
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
            $equipo = new Equipo();
            $registro = $equipo->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $equipo = new Equipo();
            $equipo->actualizar(['fecha_baja' => date('Ymd')], Campo::val('id'));

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
            $equipo = new Equipo();
            $registro = $equipo->recuperar(Campo::val('id'));
            self::sincro_form_bbdd($registro);
        }
        else
        {
            $errores = Formulario::validacion();
            if(!$errores)
            {
                $equipo = new Equipo();
                $equipo->actualizar([
                    'nombre'       => Campo::val('nombre'),
                    'tipo'         => Campo::val('tipo'),
                    'numero_serie' => Campo::val('numero_serie'),
                    'estado'       => Campo::val('estado')
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
                $equipo = new Equipo();
                $equipo->insertar([
                    'nombre'       => Campo::val('nombre'),
                    'tipo'         => Campo::val('tipo'),
                    'numero_serie' => Campo::val('numero_serie'),
                    'estado'       => Campo::val('estado')
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

        $equipo = new Equipo();
        $datos_consulta = $equipo->get_rows([
            'wheremayor' => ['fecha_baja' => date('Ymd')],
            'limit'      => LISTADO_TOTAL_POR_PAGINA,
            'offset'     => $offset
        ]);

        $filas = '';
        $total_registros = 0;

        foreach($datos_consulta as $registro)
        {
            $botonera = "
                <a onclick=\"fetchJSON('/equipos/cons/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-secondary\"><i class=\"bi bi-search\"></i></a>
                <a onclick=\"fetchJSON('/equipos/modi/{$registro['id']}?modo=ajax')\" data-bs-toggle=\"modal\" data-bs-target=\"#ventanaModal\" class=\"btn btn-primary\"><i class=\"bi bi-pencil-square\"></i></a>
                <a href=\"/equipos/baja/{$registro['id']}\" class=\"btn btn-danger\"><i class=\"bi bi-trash\"></i></a>
            ";

            $filas .= "
                <tr>
                    <th style=\"white-space:nowrap\" scope=\"row\">{$botonera}</th>
                    <td>{$registro['nombre']}</td>
                    <td>".($registro['tipo'] == 'S' ? 'Sobremesa' : 'Portátil')."</td>
                    <td>{$registro['numero_serie']}</td>
                    <td>{$registro['estado']}</td>
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
                    <th>Tipo</th>
                    <th>Número de serie</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                {$filas}
            </tbody>
            </table>
            {$barra_navegacion}
            <a href=\"/equipos/alta\" class=\"btn btn-primary\"><i class=\"bi bi-file-earmark-plus\"></i> Alta equipo</a>
        ";
    }
}