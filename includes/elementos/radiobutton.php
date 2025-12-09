<?php

class RadioButton extends Elemento
{
    function __construct($datos=[])
    {
        parent::__construct($datos);
        if(!$this->options) $this->options = [];
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

        $radios = '';
        $valor_actual = Campo::val($this->nombre);

        foreach($this->options as $value => $label)
        {
            $checked = ($valor_actual == $value) ? 'checked' : '';
            $radios .= "
                <div class=\"form-check\">
                    <input class=\"form-check-input\" type=\"radio\" name=\"{$this->nombre}\" id=\"{$this->nombre}_{$value}\" value=\"{$value}\" {$checked} {$this->disabled}>
                    <label class=\"form-check-label\" for=\"{$this->nombre}_{$value}\">{$label}</label>
                </div>
            ";
        }

        return "
            {$this->previo_envoltorio}
                {$this->literal_error}
                {$radios}
            {$this->post_envoltorio}
        ";
    }
}
