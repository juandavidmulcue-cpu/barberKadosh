<?php

require_once 'app/config/conexion.php';

class AuthController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
        // ❌ NO session_start() aquí
    }

    /* =========================
       LOGIN CLIENTE
    ========================== */
    public function loginCliente()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $correo   = isset($_POST['correo']) ? trim($_POST['correo']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if ($correo === '' || $password === '') {
                $error = "Correo electrónico o contraseña incorrectos, valide de nuevo";
            } else {

                $sql = "
                SELECT u.id_usuario, u.nombre, u.password
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.correo = :correo
                AND r.nombre_rol = 'cliente'
                LIMIT 1
            ";

                $stmt = $this->db->prepare($sql);

                // 🔴 SI HAY ERROR EN SQL, LO MUESTRA
                if (!$stmt) {
                    die('Error en prepare(): ' . implode(' | ', $this->db->errorInfo()));
                }

                $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {

                    $_SESSION['id']     = $user['id_usuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['rol']    = 'cliente';

                    header("Location: index.php?controller=cliente&action=perfil");
                    exit;
                }

                $error = "Correo electrónico o contraseña incorrectos, valide de nuevo";
            }
        }

        require 'app/views/auth/login_cliente.php';
    }

    /* =========================
       LOGIN BARBERO
    ========================== */
    public function loginBarbero()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $correo   = isset($_POST['correo']) ? trim($_POST['correo']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if ($correo === '' || $password === '') {
                $error = "Correo electrónico o contraseña incorrectos, valide de nuevo";
            } else {

                $sql = "
                SELECT u.id_usuario, u.nombre, u.password
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.correo = :correo
                AND r.nombre_rol = 'barbero'
                LIMIT 1
            ";

                $stmt = $this->db->prepare($sql);

                // 🔴 SI HAY ERROR EN SQL, LO MUESTRA
                if (!$stmt) {
                    die('Error en prepare(): ' . implode(' | ', $this->db->errorInfo()));
                }

                $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {

                    $_SESSION['id']     = $user['id_usuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['rol']    = 'barbero';

                    header("Location: index.php?controller=barbero&action=perfil");
                    exit;
                }

                $error = "Correo electrónico o contraseña incorrectos, valide de nuevo";
            }
        }

        require 'app/views/auth/login_barbero.php';
    }

    /* =========================
   LOGIN ADMIN
========================== */
    public function loginAdmin()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $correo   = isset($_POST['correo']) ? trim($_POST['correo']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if ($correo === '' || $password === '') {
                $error = "Correo electrónico o contraseña incorrectos, valide de nuevo";
            } else {

                $sql = "
                SELECT u.id_usuario, u.nombre, u.password
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.correo = :correo
                AND r.nombre_rol = 'admin'
                LIMIT 1
            ";

                $stmt = $this->db->prepare($sql);

                // 🔴 SI HAY ERROR EN SQL, LO MUESTRA
                if (!$stmt) {
                    die('Error en prepare(): ' . implode(' | ', $this->db->errorInfo()));
                }

                $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {

                    $_SESSION['id']     = $user['id_usuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['rol']    = 'admin';

                    header("Location: index.php?controller=admin&action=panel");
                    exit;
                }

                $error = "Correo electrónico o contraseña incorrectos, valide de nuevo";
            }
        }

        require 'app/views/auth/login_admin.php';
    }

    /* =========================
       REGISTRO CLIENTE
    ========================== */
    // AuthController.php
    public function registerCliente()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_usuario = trim($_POST['id_usuario']);
            $nombre     = trim($_POST['nombre']);
            $apellido   = trim($_POST['apellido']);
            $telefono   = trim($_POST['telefono']);
            $correo      = trim($_POST['correo']);
            $password   = $_POST['password'];

            // Validar que ningún campo esté vacío
            if (
                empty($id_usuario) || empty($nombre) || empty($apellido) ||
                empty($telefono) || empty($correo) || empty($password)
            ) {
                $error = "Todos los campos son obligatorios.";
            }
            // VALIDAR DOCUMENTO: ¿Son solo números?
            elseif (!ctype_digit($id_usuario)) {
                $error = "El documento debe contener únicamente números.";
            }
            // VALIDAR DOCUMENTO: Longitud (10 o 11)
            elseif (strlen($id_usuario) < 10 || strlen($id_usuario) > 11) {
                $error = "El documento debe tener exactamente 10 o 11 números.";
            }
            // VALIDAR TELÉFONO: ¿Son solo números?
            elseif (!ctype_digit($telefono)) {
                $error = "El teléfono debe contener únicamente números.";
            }
            // VALIDAR TELÉFONO: Longitud exacta de 10 dígitos
            elseif (strlen($telefono) !== 10) {
                $error = "El número de teléfono debe tener exactamente 10 dígitos.";
            }
            // VALIDAR CONTRASEÑA: Requisitos mínimos
            elseif (strlen($password) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                $error = "La contraseña debe tener mínimo 8 caracteres y al menos un símbolo.";
            } else {
                try {
                    $rol_id = 3; // Cliente
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $this->db->prepare(
                        "INSERT INTO usuarios 
                    (id_usuario, id_rol, nombre, apellido, telefono, correo, password)
                    VALUES 
                    (:id, :rol, :nombre, :apellido, :telefono, :correo, :password)"
                    );

                    $stmt->execute([
                        ':id'       => $id_usuario,
                        ':rol'      => $rol_id,
                        ':nombre'   => $nombre,
                        ':apellido' => $apellido,
                        ':telefono' => $telefono,
                        ':correo'   => $correo,
                        ':password' => $passwordHash
                    ]);

                    header("Location: index.php?controller=auth&action=loginCliente");
                    exit;
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = "El documento o el correo electrónico ya se encuentran registrados.";
                    } else {
                        $error = "Ocurrió un error al registrar el usuario. Intenta de nuevo.";
                    }
                }
            }
        }

        require 'app/views/auth/register.php';
    }

    /* =========================
       LOGOUT
    ========================== */
    public function logout()
    {
        session_start();

        // destruir variables
        $_SESSION = [];

        // destruir cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(session_name(), '', time() - 420, '/');
        }

        // destruir sesión
        session_destroy();

        // asegurar que no se reutilice
        session_write_close();

        header("Location: index.html");
        exit;
    }


    public function resetPassword()
    {
        // Solo muestra la vista
        require 'app/views/auth/reset_password.php';
    }

    public function cambiarPassword()
    {
        if (!isset($_GET['id'], $_GET['token'])) {
            header("Location: index.php?controller=auth&action=loginCliente");
            exit;
        }
        // Pasamos los datos a la vista
        $id = $_GET['id'];
        $token = $_GET['token'];

        require 'app/views/auth/cambiarContraseña.php';
    }
}
