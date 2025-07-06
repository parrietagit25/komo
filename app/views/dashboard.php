<?php
require_once __DIR__ . '/../includes/session.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: /login');
    exit();
}
$user = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Bienvenido, <?php echo htmlspecialchars($user['nombre_completo']); ?> 33333</h3>
    <a href="/logout" class="btn btn-danger mt-3">Cerrar sesión</a>
</div>
</body>
</html>
