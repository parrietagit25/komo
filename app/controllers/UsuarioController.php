<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../includes/session.php';

$db = new Database();
$conn = $db->connect();
$model = new Usuario($conn);

// Registrar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_user']) && !isset($_POST['id'])) {
    $model->registrar($_POST);
    header("Location: usuarios");
    exit();
}

// Editar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['edit_user'])) {
    $model->actualizar($_POST);
    header("Location: usuarios");
    exit();
}

// Eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['eliminar'])) {
    $model->eliminar($_POST['id']);
    header("Location: usuarios");
    exit();
}

//$usuarios = $model->todos_usuarios();
