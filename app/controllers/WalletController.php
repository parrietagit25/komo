<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Wallet.php';
require_once __DIR__ . '/../includes/session.php';

$db = new Database();
$conn = $db->connect();
$model = new Wallet($conn);

// Registrar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_wallet']) && !isset($_POST['id'])) {
    $model->registrar($_POST);
    header("Location: wallet");
    exit();
}

// Editar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['edit_trans'])) {
    $model->actualizar($_POST);
    header("Location: wallet");
    exit();
}

// Eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['eliminar_trans'])) {
    $model->eliminar($_POST['id']);
    header("Location: wallet");
    exit();
}

//$usuarios = $model->todos_usuarios();
