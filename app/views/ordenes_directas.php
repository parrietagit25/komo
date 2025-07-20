<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/OrdenDirecta.php';
require_once __DIR__ . '/../models/Stand.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login");
    exit();
}

// Verificar que sea usuario stand
if ($_SESSION['usuario']['tipo_usuario'] != 2) {
    header("Location: main");
    exit();
}

$db = new Database();
$conn = $db->connect();
$ordenDirectaModel = new OrdenDirecta($conn);
$standModel = new Stand($conn);

// Obtener todos los stands del usuario
$stands_usuario = $standModel->obtenerStandsPorUsuario($_SESSION['usuario']['id']);

// Si no hay stands, redirigir
if (empty($stands_usuario)) {
    $_SESSION['error'] = "No tienes stands asignados.";
    header("Location: main");
    exit();
}

// Si solo hay un stand, preseleccionarlo
$id_stand = null;
if (count($stands_usuario) == 1) {
    $id_stand = $stands_usuario[0]['id'];
} else {
    // Si hay múltiples stands, usar el seleccionado, el de la URL, o el primero
    $stand_url = isset($_GET['stand']) ? (int)$_GET['stand'] : 0;
    $stand_post = isset($_POST['id_stand']) ? (int)$_POST['id_stand'] : 0;
    
    if ($stand_post > 0) {
        $id_stand = $stand_post;
    } elseif ($stand_url > 0) {
        $id_stand = $stand_url;
    } else {
        $id_stand = $stands_usuario[0]['id'];
    }
}

// Procesar creación de orden directa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_orden'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $id_stand_seleccionado = (int)($_POST['id_stand'] ?? 0);
    
    if ($nombre && $id_stand_seleccionado > 0) {
        $id_orden = $ordenDirectaModel->crearOrdenDirecta($nombre, 0, $id_stand_seleccionado); // ID cliente = 0 temporalmente
        
        if ($id_orden) {
            // Procesar detalles de la orden
            $productos = $_POST['producto'] ?? [];
            $cantidades = $_POST['cantidad'] ?? [];
            $montos = $_POST['monto'] ?? [];
            
            $exito = true;
            for ($i = 0; $i < count($productos); $i++) {
                if (!empty($productos[$i]) && $cantidades[$i] > 0) {
                    $resultado = $ordenDirectaModel->agregarDetalleOrden(
                        $id_orden,
                        $productos[$i],
                        $cantidades[$i],
                        $montos[$i]
                    );
                    if (!$resultado) {
                        $exito = false;
                        break;
                    }
                }
            }
            
            if ($exito) {
                $_SESSION['success'] = "¡Orden directa creada exitosamente! Orden #$id_orden";
                // Redirigir a la pantalla de asignación de clientes
                header("Location: asignar_orden?id=" . $id_orden);
                exit();
            } else {
                $_SESSION['error'] = "Error al crear los detalles de la orden.";
            }
        } else {
            $_SESSION['error'] = "Error al crear la orden directa.";
        }
    } else {
        $_SESSION['error'] = "Por favor complete todos los campos requeridos.";
    }
    
    // Redirigir para evitar reenvío del formulario
    header("Location: ordenes_directas");
    exit();
}

// Obtener órdenes directas existentes del stand seleccionado
$ordenes = $ordenDirectaModel->obtenerOrdenesDirectas($id_stand);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Koomodo - Órdenes Directas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
  
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
    .form-container {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      border: 2px solid rgba(40, 199, 111, 0.3);
    }
    .product-row {
      background: rgba(255, 255, 255, 0.03);
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .btn-add-product {
      background: linear-gradient(45deg, #28C76F, #24a85f);
      border: none;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .btn-add-product:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(40, 199, 111, 0.4);
    }
    .btn-remove-product {
      background: linear-gradient(45deg, #dc3545, #c82333);
      border: none;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .btn-remove-product:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
    }
    .btn-create-order {
      background: linear-gradient(45deg, #28C76F, #24a85f);
      border: none;
      color: white;
      padding: 1rem 2rem;
      border-radius: 25px;
      font-weight: bold;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }
    .btn-create-order:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(40, 199, 111, 0.4);
    }
    .order-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: transform 0.3s ease;
    }
    .order-card:hover {
      transform: translateY(-2px);
    }
    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-weight: bold;
      font-size: 0.9rem;
    }
    .status-1 { background-color: #28C76F; }
    .status-2 { background-color: #007bff; }
    .status-3 { background-color: #dc3545; }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      
      <?php include 'menu.php'; ?>

      <!-- Área de contenido -->
      <div class="col-md-9 col-lg-10 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3><i class="bi bi-cart-plus me-2"></i>Órdenes Directas</h3>
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

        <!-- Formulario para crear orden directa -->
        <div class="form-container">
          <h4 class="mb-4"><i class="bi bi-plus-circle me-2"></i>Crear Nueva Orden Directa</h4>
          
          <form method="post" action="" id="ordenDirectaForm">
            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Descripción de la Orden</label>
                <input type="text" name="nombre" class="form-control bg-dark text-white" 
                       placeholder="Ej: Combo especial, Pedido urgente..." required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Seleccionar Stand</label>
                <select name="id_stand" class="form-select bg-dark text-white" required>
                  <?php foreach ($stands_usuario as $stand): ?>
                    <option value="<?= $stand['id'] ?>" <?= ($stand['id'] == $id_stand) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($stand['nombre']) ?> - <?= htmlspecialchars($stand['ubicacion']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div id="productos-container">
              <h5 class="mt-4 mb-3">Productos de la Orden</h5>
              
              <!-- Producto inicial -->
              <div class="product-row" id="producto-1">
                <div class="row">
                  <div class="col-md-4">
                    <label class="form-label">Nombre del Producto</label>
                    <input type="text" name="producto[]" class="form-control bg-dark text-white" 
                           placeholder="Nombre del producto" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad[]" class="form-control bg-dark text-white" 
                           placeholder="Cantidad" min="1" value="1" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Monto</label>
                    <input type="number" name="monto[]" class="form-control bg-dark text-white" 
                           placeholder="0.00" step="0.01" min="0" required>
                  </div>
                  <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-remove-product" onclick="removerProducto(this)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-3">
              <button type="button" class="btn btn-add-product" onclick="agregarProducto()">
                <i class="bi bi-plus-circle me-2"></i>Agregar Producto
              </button>
            </div>

            <div class="mt-4 text-end">
              <button type="submit" name="crear_orden" class="btn btn-create-order">
                <i class="bi bi-check-circle me-2"></i>Crear Orden Directa
              </button>
            </div>
          </form>
        </div>

        <!-- Lista de órdenes directas -->
        <div class="mt-5">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-list-ul me-2"></i>Órdenes Directas Existentes</h4>
            <?php if (count($stands_usuario) > 1): ?>
              <div class="d-flex align-items-center">
                <label class="form-label me-2 mb-0">Filtrar por Stand:</label>
                <select id="filtro-stand" class="form-select bg-dark text-white" style="width: auto;">
                  <?php foreach ($stands_usuario as $stand): ?>
                    <option value="<?= $stand['id'] ?>" <?= ($stand['id'] == $id_stand) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($stand['nombre']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endif; ?>
          </div>
          
          <?php if (!empty($ordenes)): ?>
            <?php foreach ($ordenes as $orden): ?>
              
              <div class="order-card">
                <div class="row align-items-center">
                  <div class="col-md-3">
                    <h6 class="mb-1 text-white">Orden #<?= $orden['id'] ?></h6>
                    <p class="text-white mb-0"><?= htmlspecialchars($orden['nombre']) ?></p>
                  </div>
                  <div class="col-md-2">
                    <p class="mb-1"><strong class="text-white">Cliente:</strong></p>
                    <p class="text-white mb-0">
                      <?php 
                      if ($orden['id_cliente'] > 0) {
                          echo $ordenDirectaModel->obtenerClientePorId($orden['id_cliente']);
                      } else {
                          echo '<span class="text-warning">Sin asignar</span>';
                      }
                      ?>
                    </p>
                  </div>
                  <div class="col-md-2">
                    <p class="mb-1"><strong class="text-white">Total:</strong></p>
                    <p class="text-success mb-0">$<?= number_format($ordenDirectaModel->obtenerTotalOrden($orden['id']), 2) ?></p>
                  </div>
                  <div class="col-md-2">
                    <p class="mb-1"><strong class="text-white">Fecha:</strong></p>
                    <p class="text-white mb-0"><?= date('d/m/Y H:i', strtotime($orden['fecha_log'])) ?></p>
                  </div>
                  <div class="col-md-2">
                    <p class="mb-1"><strong>Estado:</strong></p>
                    <?php
                    $estado_texto = '';
                    $estado_clase = '';
                    switch($orden['stat']) {
                        case 1: $estado_texto = 'Creado'; $estado_clase = 'status-1'; break;
                        case 2: $estado_texto = 'Terminado'; $estado_clase = 'status-2'; break;
                        case 3: $estado_texto = 'Cancelado'; $estado_clase = 'status-3'; break;
                    }
                    ?>
                    <span class="status-badge <?= $estado_clase ?>"><?= $estado_texto ?></span>
                  </div>
                  <div class="col-md-1">
                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" 
                            data-bs-target="#modalDetalle<?= $orden['id'] ?>">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Modal para ver detalles -->
              <div class="modal fade" id="modalDetalle<?= $orden['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                      <h5 class="modal-title">Detalles de Orden #<?= $orden['id'] ?></h5>
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
                          <?php 
                          $detalles = $ordenDirectaModel->obtenerDetalleOrdenDirecta($orden['id']);
                          foreach ($detalles as $detalle): 
                          ?>
                            <tr>
                              <td><?= htmlspecialchars($detalle['nombre_producto']) ?></td>
                              <td><?= $detalle['cantidad'] ?></td>
                              <td>$<?= number_format($detalle['monto'], 2) ?></td>
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
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
              <h5 class="mt-3 text-muted">No hay órdenes directas</h5>
              <p class="text-muted">Crea tu primera orden directa usando el formulario de arriba.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    let productoCount = 1;

    function agregarProducto() {
      productoCount++;
      const container = document.getElementById('productos-container');
      const newProduct = document.createElement('div');
      newProduct.className = 'product-row';
      newProduct.id = `producto-${productoCount}`;
      
      newProduct.innerHTML = `
        <div class="row">
          <div class="col-md-4">
            <label class="form-label">Nombre del Producto</label>
            <input type="text" name="producto[]" class="form-control bg-dark text-white" 
                   placeholder="Nombre del producto" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad[]" class="form-control bg-dark text-white" 
                   placeholder="Cantidad" min="1" value="1" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Monto</label>
            <input type="number" name="monto[]" class="form-control bg-dark text-white" 
                   placeholder="0.00" step="0.01" min="0" required>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-remove-product" onclick="removerProducto(this)">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      `;
      
      container.appendChild(newProduct);
    }

    function removerProducto(button) {
      const productRows = document.querySelectorAll('.product-row');
      if (productRows.length > 1) {
        button.closest('.product-row').remove();
      }
    }

    // Función para filtrar por stand
    document.getElementById('filtro-stand')?.addEventListener('change', function() {
      const standId = this.value;
      window.location.href = `ordenes_directas?stand=${standId}`;
    });
  </script>
</body>
</html> 