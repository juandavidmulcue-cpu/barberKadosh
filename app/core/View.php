<?php

/**
 * Clase View
 * -------------------------------------------------
 * Responsabilidad única (SRP): cargar y renderizar vistas,
 * pasando los datos como variables locales.
 */
class View
{
    /**
     * Renderiza una vista.
     *
     * @param string $ruta Ruta del archivo de la vista.
     * @param array  $data Datos disponibles dentro de la vista.
     */
    public static function render($ruta, $data = [])
    {
        if (!empty($data)) {
            extract($data);
        }

        require $ruta;
    }
}
