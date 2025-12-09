<?php

class Checkbox extends Elemento
{
    function __construct($datos=[])
    {
        // Por defecto el tipo es checkbox
        $datos['type'] = 'checkbox';

        // Las opciones pueden ser un array asociativo: ['valor'=>'Etiqueta']
        // Si no se pasa, será un único checkbox
        $this->options = empty($datos['options']) ? false : $datos['options'];

        parent::__construct($datos);
    }

    // Validación: si está vacío y es obligatorio
    function validar()
    {
        if(empty(Campo::val($this->nombre)))
        {
            $this->error = true;
            Formulario::$numero_errores++;
        }
    }

    // Pintar el checkbox
    function pintar()
    {
        $this->previo_pintar();

        $html = '';

        // Si hay varias opciones, pintamos un checkbox por cada una
        if($this->options)
        {
            foreach($this->options as $valor => $etiqueta)
            {
                $checked = ($valor == Campo::val($this->nombre)) ? 'checked' : '';
                $html .= "
                    <div class=\"form-check\">
                        <input class=\"form-check-input {$this->style}\" type=\"checkbox\" name=\"{$this->nombre}\" id=\"id{$this->nombre}_{$valor}\" value=\"{$valor}\" {$checked} {$this->disabled}>
                        <label class=\"form-check-label\" for=\"id{$this->nombre}_{$valor}\">
                            {$etiqueta}
                        </label>
                    </div>
                ";
            }
        }
        else // Checkbox simple
        {
            $checked = Campo::val($this->nombre) ? 'checked' : '';
            $html = "
                <div class=\"form-check\">
                    <input class=\"form-check-input {$this->style}\" type=\"checkbox\" name=\"{$this->nombre}\" id=\"id{$this->nombre}\" value=\"1\" {$checked} {$this->disabled}>
                    <label class=\"form-check-label\" for=\"id{$this->nombre}\">
                        ".Idioma::lit($this->nombre)."
                    </label>
                </div>
            ";
        }

        return "
            {$this->previo_envoltorio}
                {$this->literal_error}
                {$html}
            {$this->post_envoltorio}
        ";
    }
}
