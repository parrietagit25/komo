<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Stand.php';

$db = new Database();
$conn = $db->connect();
$stand = new Stand($conn);

// Eliminar
if (isset($_POST['eliminar_stad']) && isset($_POST['id'])) {
    echo 'Pasando por el eliminar';
    $stand->eliminar($_POST['id']);
    header("Location: stands");
    exit;
}

// Registrar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_stand'])) {
    echo 'Pasando por el reg';
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];
    $descripcion = $_POST['descripcion'];
    $stat = $_POST['stat'];
    $id_user = $_POST['id_user'];
    $stand->registrar($nombre, $ubicacion, $descripcion, $stat, $id_user);
    header("Location: stands");
    exit;
}

// Editar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['edit_stand'])) {
    echo 'Pasando por el edto';
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];
    $stand->actualizar($id, $nombre, $ubicacion);
    header("Location: stands");
    exit;
}
