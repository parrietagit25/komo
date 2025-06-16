<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../controllers/UsuarioController.php';

$db = new Database();
$conn = $db->connect(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Koomodo - Usuarios</title>
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
<body style="background-color: #0B1C3D; color: white;">

    <div class="container-fluid">
        <div class="row">
            <?php include('menu.php'); ?>
            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-10 content">
                <h4>Usuarios del sistema</h4>
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                    <i class="bi bi-plus-circle me-1"></i>Registrar Usuario
                </button>

                  <div class="table-responsive bg-dark p-3 rounded">
                    <table class="table table-bordered table-hover text-white align-middle">
                    <thead class="table-secondary text-dark">
                        <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre completo</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['usuario']) ?></td>
                        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php
                            switch ($u['tipo_usuario']) {
                                case 1: echo 'Administrador'; break;
                                case 2: echo 'Estándar'; break;
                                case 3: echo 'Regular'; break;
                            }
                            ?>
                        </td>
                        <td><?= $u['stat'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $u['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $u['id'] ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Registrar Usuario -->
    <div class="modal fade" id="modalRegistrar" tabindex="-1" aria-labelledby="modalRegistrarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="UsuarioController.php" class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarLabel">Registrar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="usuario" class="form-control mb-2" placeholder="Usuario" required>
                <input type="text" name="nombre_completo" class="form-control mb-2" placeholder="Nombre completo" required>
                <input type="email" name="email" class="form-control mb-2" placeholder="Correo electrónico" required>
                <select name="tipo_usuario" class="form-control mb-2" required>
                <option value="">Tipo de usuario</option>
                <option value="1">Administrador</option>
                <option value="2">Estándar</option>
                <option value="3">Regular</option>
                </select>
                <input type="password" name="password" class="form-control mb-2" placeholder="Contraseña" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
            </form>
        </div>
    </div>

    <?php foreach ($usuarios as $u): ?>
    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditar<?= $u['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $u['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="UsuarioController.php" class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <input type="text" name="usuario" value="<?= $u['usuario'] ?>" class="form-control mb-2" required>
                <input type="text" name="nombre_completo" value="<?= $u['nombre_completo'] ?>" class="form-control mb-2" required>
                <input type="email" name="email" value="<?= $u['email'] ?>" class="form-control mb-2" required>
                <select name="tipo_usuario" class="form-control mb-2" required>
                <option value="1" <?= $u['tipo_usuario'] == 1 ? 'selected' : '' ?>>Administrador</option>
                <option value="2" <?= $u['tipo_usuario'] == 2 ? 'selected' : '' ?>>Estándar</option>
                <option value="3" <?= $u['tipo_usuario'] == 3 ? 'selected' : '' ?>>Regular</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Actualizar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="modalEliminar<?= $u['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" action="UsuarioController.php" class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro que deseas eliminar a <strong><?= $u['nombre_completo'] ?></strong>?
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
                <input type="hidden" name="eliminar" value="1">
            </form>
        </div>
    </div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
