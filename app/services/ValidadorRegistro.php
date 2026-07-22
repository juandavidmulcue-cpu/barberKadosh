<?php

/**
 * ValidadorRegistro
 * -------------------------------------------------
 * Clase de apoyo (SRP): concentra las reglas de
 * validación del formulario de registro de cliente.
 * Antes esta lógica vivía dentro del método de registro
 * del controlador, mezclada con el flujo HTTP.
 */
class ValidadorRegistro
{
    /**
     * Valida los datos del formulario.
     *
     * @return string|null Mensaje de error, o null si todo es válido.
     */
    public function validar(array $datos)
    {
        if ($this->hayCamposVacios($datos)) {
            return "Todos los campos son obligatorios";
        }

        if (!$this->documentoValido($datos['id_usuario'])) {
            return "Documento inválido (solo números, entre 10 y 11 dígitos)";
        }

        if (!$this->telefonoValido($datos['telefono'])) {
            return "Teléfono inválido (10 dígitos numéricos)";
        }

        if (!$this->passwordFuerte($datos['password'])) {
            return "Contraseña débil (mínimo 8 caracteres y un símbolo)";
        }

        return null;
    }

    private function hayCamposVacios(array $datos)
    {
        foreach (['id_usuario', 'nombre', 'apellido', 'telefono', 'correo', 'password'] as $campo) {
            if (empty($datos[$campo])) {
                return true;
            }
        }
        return false;
    }

    private function documentoValido($documento)
    {
        return ctype_digit($documento)
            && strlen($documento) >= 10
            && strlen($documento) <= 11;
    }

    private function telefonoValido($telefono)
    {
        return ctype_digit($telefono) && strlen($telefono) === 10;
    }

    private function passwordFuerte($password)
    {
        return strlen($password) >= 8
            && preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
    }
}
