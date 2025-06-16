<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../includes/session.php';

$db = new Database();
$conn = $db->connect();
$model = new Evento($conn);

// Registrar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && !isset($_POST['id'])) {
    $model->registrar($_POST);
    header("Location: eventos");
    exit();
}

// Editar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !isset($_POST['eliminar'])) {
    $model->actualizar($_POST);
    header("Location: eventos");
    exit();
}

// Eliminar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['eliminar'])) {
    $model->eliminar($_POST['id']);
    header("Location: eventos");
    exit();
}
