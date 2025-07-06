<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
//require_once __DIR__ . '/../controllers/MisproductosController.php';
require_once __DIR__ . '/../models/Productos.php';

$db     = new Database();
$conn   = $db->connect();
$model  = new Productos($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_producto'])) {
$model->guardar_producto();
}elseif (isset($_POST['eliminar_producto'])) {
  $model->eliminar_producto($_POST['id_producto']);
}

$stands = $model->obtenerStand($_SESSION['usuario']['id']);

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
        <h4>Mis Stands</h4>
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
              <td><?= htmlspecialchars($s['descripcion']) ?></td>
              <td><?= $s['estado'] ?></td>
              <td class="text-center">
                <!-- Botón para registrar producto -->
                <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal"
                        data-bs-target="#modalRegistrarProducto<?= $s['id'] ?>">
                  <i class="bi bi-plus-circle"></i>
                </button>

                <!-- Botón para ver productos -->
                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalProductos<?= $s['id'] ?>">
                  <i class="bi bi-box-seam"></i>
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

  <!-- Modal Ver Productos -->
<div class="modal fade" id="modalProductos<?= $s['id'] ?>" tabindex="-1"
     aria-labelledby="labelProductos<?= $s['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="labelProductos<?= $s['id'] ?>">Productos del Stand: <?= htmlspecialchars($s['nombre']) ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-hover text-white">
          <thead class="table-secondary text-dark">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Foto</th>
              <th>Costo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $productos = $model->obtenerProductosPorStand($s['id']);
              foreach ($productos as $p):
            ?>
              <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nombre_producto']) ?></td>
                <td>
                  <?php if ($p['foto']): ?>
                    <img src="<?= htmlspecialchars('../app/'.$p['foto']) ?>" alt="Foto" width="50">
                  <?php else: ?>
                    <em>Sin imagen</em>
                  <?php endif; ?>
                </td>
                <td>$<?= number_format($p['costo'], 2) ?></td>
                <td><?= $p['stat'] ?></td>
                <td class="text-center">
                  <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                          data-bs-target="#modalEliminarProducto<?= $p['id'] ?>">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Registrar Producto -->
<div class="modal fade" id="modalRegistrarProducto<?= $s['id'] ?>" tabindex="-1"
     aria-labelledby="labelRegistrarProducto<?= $s['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" action="mis_productos" enctype="multipart/form-data" class="modal-content bg-dark text-white">

      <div class="modal-header">
        <h5 class="modal-title" id="labelRegistrarProducto<?= $s['id'] ?>">
          Registrar Producto - Stand: <?= htmlspecialchars($s['nombre']) ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="id_stand" value="<?= $s['id'] ?>">
        <input type="hidden" name="id_user" value="<?= $_SESSION['usuario']['id'] ?>">

        <label class="form-label">Nombre del producto</label>
        <input type="text" name="nombre_producto" class="form-control mb-2" required>

        <label class="form-label">Foto (opcional)</label>
        <input type="file" name="foto" accept="image/*" class="form-control mb-2">

        <label class="form-label">Costo ($)</label>
        <input type="number" step="0.01" name="costo" class="form-control mb-2" required>

        <label class="form-label">Estado</label>
        <select name="stat" class="form-select">
          <option value="1">Activo</option>
          <option value="2">Inactivo</option>
        </select>
      </div>

      <div class="modal-footer">
        <button type="submit" name="reg_producto" class="btn btn-success">Registrar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>


<?php endforeach; ?>

<!-- Modal Registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <form method="post" action="" enctype="multipart/form-data">

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

<?php foreach ($stands as $s): ?>
  <?php $productos = $model->obtenerProductosPorStand($s['id']); ?>
  <?php foreach ($productos as $p): ?>
    <!-- Modal Confirmar Eliminación (FUERA del modal de productos) -->
    <div class="modal fade" id="modalEliminarProducto<?= $p['id'] ?>" tabindex="-1"
         aria-labelledby="labelEliminarProducto<?= $p['id'] ?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="" class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="labelEliminarProducto<?= $p['id'] ?>">Confirmar Eliminación</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            ¿Estás seguro de que deseas eliminar el producto <strong><?= htmlspecialchars($p['nombre_producto']) ?></strong>?
            <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
            <input type="hidden" name="confirmar_eliminacion" value="1">
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger" name="eliminar_producto">Eliminar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
<?php endforeach; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
