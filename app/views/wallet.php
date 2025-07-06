<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../controllers/WalletController.php';
require_once __DIR__ . '/../models/Wallet.php';
require_once __DIR__ . '/../models/Usuario.php';

$db = new Database();
$conn = $db->connect();
$usuarios = new Usuario($conn);
$todos_usuarios = $usuarios->todos_usuarios();
$wallet = new Wallet($conn);
$todas_transacciones = $wallet->ver_todas_transacciones();


?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Koomodo - Wallet</title>
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
                <h4>Cash</h4>
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                    <i class="bi bi-plus-circle me-1"></i>Registrar Transaccion
                </button>

                  <div class="table-responsive bg-dark p-3 rounded">
                    <table class="table table-bordered table-hover text-white align-middle">
                        <thead class="table-secondary text-dark">
                            <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($todas_transacciones as $u): ?>
                            <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($u['monto']) ?></td>
                            <td><?= htmlspecialchars($u['fecha_log']) ?></td>
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
            <form method="post" action="" class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRegistrarLabel">Registrar Transaccion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="">Seleccionar Usuario</label>
                    <select name="id_user" class="form-select mb-2" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($todos_usuarios as $u): ?>
                            <option value="<?= $u['id']; ?>">
                            <?= htmlspecialchars($u['nombre_completo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="">Monto</label>
                    <input type="text" name="monto" class="form-control mb-2" placeholder="Monto" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="reg_wallet">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <?php foreach ($todas_transacciones as $u): ?>
    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditar<?= $u['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $u['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="" class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Editar Transaccion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <label for="">Seleccionar Usuario</label>
                <select name="id_user" class="form-select mb-2" required>
                    <?php foreach ($todos_usuarios as $uu): ?>
                        <option value="<?= $uu['id']; ?>" <?php if($uu['id'] == $u['id_user']){ echo 'selected'; } ?>>
                        <?= htmlspecialchars($uu['nombre_completo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="">Monto</label>
                <input type="text" name="monto" class="form-control mb-2" placeholder="Monto" required value="<?= htmlspecialchars($u['monto']) ?>">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning" name="edit_trans">Actualizar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
            </form>
        </div>
    </div>
    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="modalEliminar<?= $u['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" action="" class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Transaccion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro que deseas eliminar la transaccion de <strong><?= $u['nombre_completo'] ?></strong>?
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
                <input type="hidden" name="eliminar_trans" value="1">
            </form>
        </div>
    </div>
    <?php endforeach; ?>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
