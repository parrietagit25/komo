<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Stand.php';

$db     = new Database();
$conn   = $db->connect();
$model  = new Stand($conn);
$stands = $model->obtenerTodosStand();

/* Si necesitas $usuarios para el modal de registro,
   cárgalos aquí con tu modelo correspondiente */
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Koomodo - Stands</title>

  <!-- Bootstrap & Bootstrap-icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    body         { background-color:#0B1C3D; color:#fff; }
    .sidebar     { background-color:#1e1e2f; height:100vh; padding-top:1rem; }
    .sidebar a   { color:#ccc; text-decoration:none; display:block; padding:12px 20px; border-radius:8px; }
    .sidebar a:hover,.sidebar a.active{ background-color:#28C76F; color:#fff; }
    .content     { padding:2rem; }
  </style>
</head>

<body>
<div class="container-fluid">
  <div class="row">

    <?php include 'menu.php'; ?>

    <!-- Área de contenido -->
    <div class="col-md-9 col-lg-10 content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Stands</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
          Registrar Stand
        </button>
      </div>

      <div class="table-responsive bg-dark p-3 rounded">
        <table class="table table-bordered table-hover text-white align-middle">
          <thead class="table-secondary text-dark">
            <tr>
              <th>ID</th>
              <th>Encargado</th>
              <th>Nombre</th>
              <th>Ubicación</th>
              <th>Descripción</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>

          <tbody>
          <?php foreach ($stands as $s): ?>
            <tr>
              <td><?= $s['id'] ?></td>
              <td><?= $s['nombre_completo'] ?></td>
              <td><?= htmlspecialchars($s['nombre']) ?></td>
              <td><?= htmlspecialchars($s['ubicacion']) ?></td>
              <td><?= $s['estado'] ?></td>
              <td><?= htmlspecialchars($s['descripcion']) ?></td>
              <td class="text-center">
                <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditar<?= $s['id'] ?>">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEliminar<?= $s['id'] ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div><!-- /content -->
  </div><!-- /row -->
</div><!-- /container -->

<!-- ░░░░░░░░░░░░░░░  MODALES  ░░░░░░░░░░░░░░░ -->
<?php foreach ($stands as $s): ?>
  <!-- Modal Editar -->
  <div class="modal fade" id="modalEditar<?= $s['id'] ?>" tabindex="-1"
       aria-labelledby="labelEditar<?= $s['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="post" action=""
            class="modal-content bg-dark text-white">

        <div class="modal-header">
          <h5 class="modal-title" id="labelEditar<?= $s['id'] ?>">Editar Stand</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" value="<?= $s['id'] ?>">

          <label class="form-label">Nombre del stand</label>
          <input type="text" name="nombre"
                 value="<?= htmlspecialchars($s['nombre']) ?>"
                 class="form-control mb-2" required>

          <label class="form-label">Ubicación</label>
          <input type="text" name="ubicacion"
                 value="<?= htmlspecialchars($s['ubicacion']) ?>"
                 class="form-control mb-2">

          <label class="form-label">Descripción</label>
          <textarea name="descripcion"
                    class="form-control mb-2"><?= htmlspecialchars($s['descripcion']) ?></textarea>

          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="Activo"   <?= $s['estado']==='Activo'   ? 'selected' : '' ?>>Activo</option>
            <option value="Inactivo" <?= $s['estado']==='Inactivo' ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning" name="edit_stand">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>

      </form>
    </div>
  </div>

  <!-- Modal Eliminar -->
  <div class="modal fade" id="modalEliminar<?= $s['id'] ?>" tabindex="-1"
       aria-labelledby="labelEliminar<?= $s['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="post" action=""
            class="modal-content bg-dark text-white">

        <div class="modal-header">
          <h5 class="modal-title" id="labelEliminar<?= $s['id'] ?>" >Eliminar Stand</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          ¿Estás seguro de eliminar <strong><?= htmlspecialchars($s['nombre']) ?></strong>?
          <input type="hidden" name="id" value="<?= $s['id'] ?>">
          <input type="hidden" name="eliminar_stad" value="1">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Eliminar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>

      </form>
    </div>
  </div>
<?php endforeach; ?>

<!-- Modal Registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" action="StandController.php"
          class="modal-content bg-dark text-white">

      <div class="modal-header">
        <h5 class="modal-title">Registrar Stand</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label class="form-label">Seleccione usuario</label>
        <select name="id_user" class="form-select mb-2" required>
          <option value="">Seleccionar</option>
          <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id']; ?>">
              <?= htmlspecialchars($u['nombre_completo']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label class="form-label">Nombre del stand</label>
        <input type="text" name="nombre" class="form-control mb-2" required>

        <label class="form-label">Ubicación</label>
        <input type="text" name="ubicacion" class="form-control mb-2">

        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control mb-2"></textarea>

        <label class="form-label">Estado</label>
        <select name="stat" class="form-select">
          <option value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
        </select>
      </div>

      <div class="modal-footer">
        <button type="submit" name="reg_stand" class="btn btn-success">Registrar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
