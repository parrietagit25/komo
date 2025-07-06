<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../includes/session.php';

$db = new Database();
$conn = $db->connect();

$usuarioModel = new Usuario($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $usuarioModel->validarUsuario($usuario, $password);

    if ($user) {

        $_SESSION['usuario'] = $user;
        header("Location: main"); 
        exit();
    } else {
        header("Location: login?error=1");
        exit();
    }
}
