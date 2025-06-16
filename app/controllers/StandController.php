<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Stand.php';

$db = new Database();
$conn = $db->connect();
$stand = new Stand($conn);

// Eliminar
if (isset($_POST['eliminar']) && isset($_POST['id'])) {
    $stand->eliminar($_POST['id']);
    header("Location: /stands");
    exit;
}

// Registrar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['id'])) {
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];
    $stand->registrar($nombre, $ubicacion);
    header("Location: /stands");
    exit;
}

// Editar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];
    $stand->actualizar($id, $nombre, $ubicacion);
    header("Location: /stands");
    exit;
}
