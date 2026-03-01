<?php

class Checkbox extends Elemento
{
    function __construct($datos=[])
    {
        $datos['type'] = 'checkbox';
        $this->options = empty($datos['options']) ? false : $datos['options'];
        parent::__construct($datos);
    }

    function validar()
    {
        if(empty(Campo::val($this->nombre)))
        {
            $this->error = true;
            Formulario::$numero_errores++;
        }
    }

    function pintar()
    {
        $this->previo_pintar();

        $html = '';

        if($this->options)
        {
            foreach($this->options as $valor => $etiqueta)
            {
                $checked = ($valor == Campo::val($this->nombre)) ? 'checked' : '';
                $html .= "
                    <div class=\"form-check\">
                        <input class=\"form-check-input\" type=\"checkbox\" name=\"{$this->nombre}\" id=\"id{$this->nombre}_{$valor}\" value=\"{$valor}\" {$checked} {$this->disabled}>
                        <label class=\"form-check-label\" for=\"id{$this->nombre}_{$valor}\">
                            {$etiqueta}
                        </label>
                    </div>
                ";
            }
        }
        else
        {
            $checked = Campo::val($this->nombre) ? 'checked' : '';
            $html = "
                <div class=\"form-check\">
                    <input class=\"form-check-input\" type=\"checkbox\" name=\"{$this->nombre}\" id=\"id{$this->nombre}\" value=\"1\" {$checked} {$this->disabled}
                        onchange=\"document.getElementById('bloque_grupo').style.display = this.checked ? 'block' : 'none'\">
                    <label class=\"form-check-label\" for=\"id{$this->nombre}\">
                        ". Idioma::lit($this->nombre) ."
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