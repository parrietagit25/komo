<?php
require_once __DIR__ . '/../includes/session.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Koomodo - Panel Principal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #0B1C3D;
      color: white;
    }
    .sidebar {
      background-color: #1e1e2f;
      height: 100vh;
      padding-top: 1rem;
    }
    .sidebar a {
      color: #ccc;
      text-decoration: none;
      display: block;
      padding: 12px 20px;
      border-radius: 8px;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #28C76F;
      color: white;
    }
    .content {
      padding: 2rem;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">

        <?php include('menu.php'); ?>

      <!-- Contenido principal -->
      <div class="col-md-9 col-lg-10 content">
        <h3>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre_completo']); ?></h3>
        <p class="text-white-50">Selecciona una opción del menú lateral para comenzar.</p>
      </div>
    </div>
  </div>
</body>
</html>
