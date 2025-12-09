<?php

    class Number extends Elemento
    {

        function __construct($datos = [])
        {
            // Forzamos el tipo number
            $datos['type'] = 'number';

            // Si quieres permitir decimales, podrías añadir min/max/step aquí,
            // pero lo dejo limpio para mantener el mismo estilo que Text.
            
            parent::__construct($datos);
        }

        function validar()
        {
            // Igual que Text: campo obligatorio si está vacío
            if (empty(Campo::val($this->nombre)) && Campo::val($this->nombre) !== "0") {
                $this->error = True;
                Formulario::$numero_errores++;
            }
        }

    }
