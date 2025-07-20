<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/koomoeventos.php';

$db     = new Database();
$conn   = $db->connect();
$model  = new Komodoeventos($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_orden'])) {
    echo 'Pasando';
$model->guardar_orden();
}
$stands = $model->obtenerStand();


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

      <!-- Mensajes de éxito y error -->
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <div class="table-responsive bg-dark p-3 rounded">
        <table class="table table-bordered table-hover text-white align-middle">
          <thead class="table-secondary text-dark">
            <tr>
              <th>ID</th>
              <th>Establecimiento</th>
              <th>Ubicación</th>
              <th>Descripción</th>
              <th>Acciones</th>
            </tr>
          </thead>

          <tbody>

          <?php foreach ($stands as $s): ?>
            <tr>
              <td><?= $s['id'] ?></td>
              <td><?= $s['nombre'] ?></td>
              <td><?= htmlspecialchars($s['ubicacion']) ?></td>
              <td><?= htmlspecialchars($s['descripcion']) ?></td>
              <td class="text-center">
                <!-- Botón para ver productos -->
                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalProductos<?= $s['id'] ?>">
                  <i class="bi bi-inboxes"></i>
                </button>
                <!-- Botón para registrar producto -->
                <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal"
                        data-bs-target="#modalRegistrarProducto<?= $s['id'] ?>">
                  <i class="bi bi-eye"></i>
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

  <!-- Modal Ver Productos -->
<div class="modal fade" id="modalProductos<?= $s['id'] ?>" tabindex="-1"
     aria-labelledby="labelProductos<?= $s['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form method="post" action="" enctype="multipart/form-data" class="modal-content bg-dark text-white">
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
                <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $productos = $model->obtenerProductosPorStand($s['id']);
                foreach ($productos as $p):
                ?>
                <tr>
                    <td><?= $p['id'] ?><input type="hidden" name="id_produc[]" value="<?= $p['id'] ?>"></td>
                    <td><?= htmlspecialchars($p['nombre_producto']) ?></td>
                    <td>
                    <?php if ($p['foto']): ?>
                        <img src="<?= htmlspecialchars('../app/'.$p['foto']) ?>" alt="Foto" width="50">
                    <?php else: ?>
                        <em>Sin imagen</em>
                    <?php endif; ?>
                    </td>
                    <td>$<?= number_format($p['costo'], 2) ?><input type="hidden" name="product_costo[]" value="<?= $p['costo'] ?>"></td>
                    <td class="text-center">
                        <input type="number" 
                            name="cantidad_pro[]" 
                            id="cantidad_pro<?= $p['id'] ?>" 
                            class="form-control cantidad-input" 
                            value="0" 
                            data-costo="<?= $p['costo'] ?>" 
                            style="width:80px;" 
                            oninput="sumar_orden()">
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3">Totales</td>
                    <td id="costo"></td>
                    <td id="cantidad"></td>
                </tr>

            </tbody>
            </table>
            <input type="hidden" id="costo_total" name="costo_product[]">
            <input type="hidden" id="cantidad_total" name="cantidad_product[]">
            <input type="hidden" name="id_stand" value="<?php echo $s['id']; ?>">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" name="reg_orden" class="btn btn-success">Registrar</button>
        </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal Registrar Producto -->
 
<div class="modal fade" id="modalRegistrarProducto<?= $s['id'] ?>" tabindex="-1"
     aria-labelledby="labelRegistrarProducto<?= $s['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content bg-dark text-white">

      <div class="modal-header">
        <h5 class="modal-title" id="labelRegistrarProducto<?= $s['id'] ?>">
          Ordenes Realizadas
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?php $ordenes = $model->obtener_ordenes($s['id'], $_SESSION['usuario']['id']); ?>
        <?php if (count($ordenes) > 0): ?>
        <table class="table table-sm table-hover table-bordered text-white">
            <thead class="table-secondary text-dark">
            <tr>
                <th># Orden</th>
                <th>Monto Total</th>
                <th>Cant. Productos</th>
                <th>Fecha</th>
                <th>Ver</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ordenes as $o): ?>
            <tr>
                <td><?= $o['id'] ?></td>
                <td>$<?= number_format($o['monto_total'], 2) ?></td>
                <td><?= $o['cantidad_productos'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($o['fecha_log'])) ?></td>
                <td>
                <a class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetalleOrden<?= $o['id'] ?>">
                    <i class="bi bi-eye"></i>
                </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No hay órdenes registradas para este stand.</p>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<?php $ordenes = $model->obtener_ordenes($s['id'], $_SESSION['usuario']['id']); ?>
<?php foreach ($ordenes as $o): 
  $detalles = $model->obtener_detalle_orden($o['id']);
?>
<!-- Modal Detalle de la Orden -->
<div class="modal fade" id="modalDetalleOrden<?= $o['id'] ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de la Orden #<?= $o['id'] ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered text-white">
          <thead class="table-secondary text-dark">
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Monto</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($detalles as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['nombre_producto']) ?></td>
              <td><?= $d['cantidad'] ?></td>
              <td>$<?= number_format($d['monto'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function sumar_orden() {
    let cantidadTotal = 0;
    let costoTotal = 0;

    // Seleccionar todos los inputs de cantidad
    document.querySelectorAll('.cantidad-input').forEach(input => {
        let cantidad = parseInt(input.value) || 0;
        let costoUnitario = parseFloat(input.getAttribute('data-costo')) || 0;

        cantidadTotal += cantidad;
        costoTotal += cantidad * costoUnitario;
    });

    // Mostrar los resultados en los TD correspondientes
    document.getElementById('cantidad').innerText = cantidadTotal;
    document.getElementById('costo').innerText = '$' + costoTotal.toFixed(2);
    document.getElementById('cantidad_total').value = cantidadTotal;
    document.getElementById('costo_total').value = costoTotal.toFixed(2);
}
</script>

</body>
</html>
