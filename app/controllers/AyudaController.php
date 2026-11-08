
<?php

class AyudaController
{
    public function principal()
    {
        $this->mostrar('app/views/manual/ayudaIndex.php');
    }

    public function cliente()
    {
        $this->mostrar('app/views/manual/ayudaCliente.php');
    }

    public function barbero()
    {
        $this->mostrar('app/views/manual/ayudaBarbero.php');
    }

    public function admin()
    {
        $this->mostrar('app/views/manual/ayudaAdmin.php');
    }

    private function mostrar($pagina)
    {
        $ruta = dirname(__DIR__, 2) . '/' . $pagina;

        if (!is_file($ruta)) {
            http_response_code(404);
            require dirname(__DIR__) . '/views/errors/error.php';
            return;
        }

        require $ruta;
    }
}
