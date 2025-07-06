<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../includes/session.php';

$db = new Database();
$conn = $db->connect();
$model = new Evento($conn);

// Registrar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_evento']) && !isset($_POST['id'])) {
    $model->registrar($_POST);
    header("Location: evento");
    exit();
}

// Editar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['editar_evento'])) {
    $model->actualizar($_POST);
    header("Location: evento");
    exit();
}

// Eliminar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['eliminar_evento'])) {
    $model->eliminar($_POST['id']);
    header("Location: evento");
    exit();
}

// asignar estand
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asig_stand']) && !isset($_POST['id'])) {
    $model->asignar_stand($_POST);
    header("Location: evento");
    exit();
}