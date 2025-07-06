<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Stand.php';


$db      = new Database();
$conn    = $db->connect();
$model   = new Evento($conn);
$eventos = $model->obtenerTodosEventos();
//$ver_asignados = $model->ver_asignados();
$model_stand = new Stand($conn);
$stand = $model_stand->obtenerTodosStand();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Koomodo - Eventos</title>

  <!-- Bootstrap + Icons -->
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

    <div class="col-md-9 col-lg-10 content">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Eventos</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
          Registrar Evento
        </button>
      </div>

      <div class="table-responsive bg-dark p-3 rounded">
        <table class="table table-bordered table-hover text-white align-middle">
          <thead class="table-secondary text-dark">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Fecha</th>
              <th>Ubicación</th>
              <th>Descripción</th>
              <th>Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($eventos as $e): ?>
            <tr>
              <td><?= $e['id'] ?></td>
              <td><?= htmlspecialchars($e['nombre']) ?></td>
              <td><?= $e['fecha'] ?></td>
              <td><?= htmlspecialchars($e['ubicacion']) ?></td>
              <td><?= htmlspecialchars($e['descripcion']) ?></td>
              <td><?= $e['estado'] ?></td>
              <td class="text-center">
                <button class="btn btn-info btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalAsignarStand<?= $e['id'] ?>">
                  <i class="bi bi-diagram-3"></i>
                </button>
                <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditar<?= $e['id'] ?>">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEliminar<?= $e['id'] ?>">
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

<!-- ░░░░░░░░░░░░░  MODALES (fuera de la tabla)  ░░░░░░░░░░░░░ -->
<?php foreach ($eventos as $e): ?>
  <!-- Modal Editar -->
  <div class="modal fade" id="modalEditar<?= $e['id'] ?>" tabindex="-1"
       aria-labelledby="labelEditar<?= $e['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="post" action="EventoController.php"
            class="modal-content bg-dark text-white">

        <div class="modal-header">
          <h5 class="modal-title" id="labelEditar<?= $e['id'] ?>">Editar Evento</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" value="<?= $e['id'] ?>">

          <label class="form-label">Nombre</label>
          <input type="text" name="nombre"
                 value="<?= htmlspecialchars($e['nombre']) ?>"
                 class="form-control mb-2" required>

          <label class="form-label">Fecha</label>
          <input type="date" name="fecha"
                 value="<?= $e['fecha'] ?>"
                 class="form-control mb-2" required>

          <label class="form-label">Ubicación</label>
          <input type="text" name="ubicacion"
                 value="<?= htmlspecialchars($e['ubicacion']) ?>"
                 class="form-control mb-2">

          <label class="form-label">Descripción</label>
          <textarea name="descripcion"
                    class="form-control mb-2"><?= htmlspecialchars($e['descripcion']) ?></textarea>

          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="Activo"   <?= $e['estado']==='Activo'   ? 'selected' : '' ?>>Activo</option>
            <option value="Inactivo" <?= $e['estado']==='Inactivo' ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning" name="editar_evento">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>

      </form>
    </div>
  </div>

  <!-- Modal Eliminar -->
  <div class="modal fade" id="modalEliminar<?= $e['id'] ?>" tabindex="-1"
       aria-labelledby="labelEliminar<?= $e['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="post" action="EventoController.php"
            class="modal-content bg-dark text-white">

        <div class="modal-header">
          <h5 class="modal-title" id="labelEliminar<?= $e['id'] ?>">Eliminar Evento</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          ¿Estás seguro de eliminar <strong><?= htmlspecialchars($e['nombre']) ?></strong>?
          <input type="hidden" name="id" value="<?= $e['id'] ?>">
          <input type="hidden" name="eliminar_evento" value="1">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Eliminar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>

      </form>
    </div>
  </div>

  <!-- Modal asignar stand -->
  <div class="modal fade" id="modalAsignarStand<?= $e['id'] ?>" tabindex="-1"
       aria-labelledby="labelEditar<?= $e['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="post" action=""
            class="modal-content bg-dark text-white">

        <div class="modal-header">
          <h5 class="modal-title" id="labelEditar<?= $e['id'] ?>">Asignar Stand al evento</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id_even" value="<?= $e['id'] ?>">
          <label class="form-label">Estado</label>
          <select name="id_stand" class="form-select">
            <option value="">Seleccionar</option>
            <?php foreach ($stand as $u): ?>
              <option value="<?= $u['id']; ?>">
                <?= htmlspecialchars($u['nombre_completo']); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <label class="form-label">Comentario</label>
          <textarea class="form-control mb-2" name="comentario"></textarea>

          <br>
          <h3>Stand asignados en este evento</h3>
            <div class="table-responsive bg-dark p-3 rounded">
              <table class="table table-bordered table-hover text-white align-middle">
                <thead class="table-secondary text-dark">
                  <tr>
                    <th>Evento</th>
                    <th>Stand</th>
                    <th>Fecha Asignacion</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($model->ver_asignados($e['id']) as $e): ?>
                  <tr>
                    <td><?= $e['nombre_evento'] ?></td>
                    <td><?= $e['nombre_stand'] ?></td>
                    <td><?= $e['fecha_log'] ?></td>
                    <td><?= $e['stat'] ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-warning" name="asig_stand">Asignar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>

      </form>
    </div>
  </div>

<?php endforeach; ?>

<!-- Modal Registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" action="EventoController.php"
          class="modal-content bg-dark text-white">

      <div class="modal-header">
        <h5 class="modal-title">Registrar Evento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control mb-2" required>

        <label class="form-label">Fecha</label>
        <input type="date" name="fecha" class="form-control mb-2" required>

        <label class="form-label">Ubicación</label>
        <input type="text" name="ubicacion" class="form-control mb-2">

        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control mb-2"></textarea>

        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
          <option value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
        </select>
      </div>

      <div class="modal-footer">
        <button type="submit" name="reg_evento" class="btn btn-success">Registrar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
