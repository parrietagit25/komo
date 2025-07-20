<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Productos.php';
require_once __DIR__ . '/../models/Stand.php';
require_once __DIR__ . '/../models/koomoeventos.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login");
    exit();
}

$db = new Database();
$conn = $db->connect();
$productosModel = new Productos($conn);
$standModel = new Stand($conn);
$ordenModel = new Komodoeventos($conn);

// Obtener el ID del stand desde la URL
$stand_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($stand_id <= 0) {
    header("Location: main");
    exit();
}

// Obtener información del stand
$stand = $standModel->obtenerStandPorId($stand_id);
if (!$stand) {
    header("Location: main");
    exit();
}

// Obtener productos del stand
$productos = $productosModel->obtenerProductosPorStand($stand_id);

// Procesar creación de orden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_orden'])) {
    $resultado = $ordenModel->guardar_orden();
    if ($resultado) {
        // Redirigir a kooomo_eventos con mensaje de éxito
        header("Location: kooomo_eventos");
        exit();
    } else {
        // Mantener en la misma página si hay error
        // Los mensajes de error se mostrarán automáticamente
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Koomodo - Productos del Stand</title>
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
    .stand-header {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      border-left: 4px solid #28C76F;
    }
    .product-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(40, 199, 111, 0.2);
    }
    .product-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 1rem;
    }
    .price-tag {
      background: linear-gradient(45deg, #28C76F, #24a85f);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 25px;
      font-weight: bold;
      display: inline-block;
      margin-top: 1rem;
    }
    .btn-comprar {
      background: linear-gradient(45deg, #28C76F, #24a85f);
      border: none;
      color: white;
      padding: 0.75rem 2rem;
      border-radius: 25px;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .btn-comprar:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(40, 199, 111, 0.4);
    }
    .no-products {
      text-align: center;
      padding: 3rem;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
    }
    .back-button {
      background-color: transparent;
      border: 2px solid #28C76F;
      color: #28C76F;
      padding: 0.5rem 1.5rem;
      border-radius: 25px;
      transition: all 0.3s ease;
    }
    .back-button:hover {
      background-color: #28C76F;
      color: white;
    }
    .quantity-input {
      width: 80px;
      text-align: center;
      border-radius: 8px;
      border: 1px solid #28C76F;
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
    }
    .order-summary {
      background: rgba(40, 199, 111, 0.1);
      border: 2px solid #28C76F;
      border-radius: 15px;
      padding: 1.5rem;
      margin-top: 2rem;
    }
    .btn-orden {
      background: linear-gradient(45deg, #28C76F, #24a85f);
      border: none;
      color: white;
      padding: 1rem 2rem;
      border-radius: 25px;
      font-weight: bold;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }
    .btn-orden:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(40, 199, 111, 0.4);
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      
      <?php include 'menu.php'; ?>

      <!-- Área de contenido -->
      <div class="col-md-9 col-lg-10 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <button onclick="history.back()" class="btn back-button me-3">
              <i class="bi bi-arrow-left me-2"></i>Volver
            </button>
            <h3><i class="bi bi-shop me-2"></i>Productos del Stand</h3>
          </div>
        </div>

        <!-- Información del Stand -->
        <div class="stand-header">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h4 class="mb-2"><?= htmlspecialchars($stand['nombre']) ?></h4>
              <p class="text-muted mb-2">
                <i class="bi bi-geo-alt me-2"></i><?= htmlspecialchars($stand['ubicacion']) ?>
              </p>
              <p class="mb-0">
                <i class="bi bi-person me-2"></i>Encargado: <?= htmlspecialchars($stand['encargado']) ?>
              </p>
            </div>
            <div class="col-md-4 text-end">
              <span class="badge bg-success fs-6">Stand #<?= $stand['id'] ?></span>
            </div>
          </div>
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

        <!-- Formulario de Orden -->
        <form method="post" action="" id="ordenForm">
          <input type="hidden" name="id_stand" value="<?= $stand_id ?>">
          
          <!-- Productos -->
          <?php if (!empty($productos)): ?>
            <div class="row">
              <?php foreach ($productos as $producto): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                  <div class="product-card h-100">
                    <div class="text-center">
                      <?php if ($producto['foto']): ?>
                        <img src="<?= htmlspecialchars('../app/'.$producto['foto']) ?>" 
                             alt="<?= htmlspecialchars($producto['nombre_producto']) ?>" 
                             class="product-image">
                      <?php else: ?>
                        <div class="product-image d-flex align-items-center justify-content-center bg-secondary">
                          <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                    
                    <h5 class="mb-2"><?= htmlspecialchars($producto['nombre_producto']) ?></h5>
                    
                    <div class="price-tag">
                      $<?= number_format($producto['costo'], 2) ?>
                    </div>
                    
                    <div class="mt-3">
                      <label class="form-label">Cantidad:</label>
                      <input type="number" 
                             name="cantidad_pro[]" 
                             class="form-control quantity-input cantidad-input" 
                             value="0" 
                             min="0"
                             data-costo="<?= $producto['costo'] ?>" 
                             oninput="sumar_orden()">
                      <input type="hidden" name="id_produc[]" value="<?= $producto['id'] ?>">
                      <input type="hidden" name="product_costo[]" value="<?= $producto['costo'] ?>">
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Resumen de la Orden -->
            <div class="order-summary">
              <div class="row align-items-center">
                <div class="col-md-6">
                  <h5 class="mb-3"><i class="bi bi-cart-check me-2"></i>Resumen de tu Orden</h5>
                  <div class="row">
                    <div class="col-6">
                      <p class="mb-1">Total Productos:</p>
                      <h6 id="cantidad" class="text-success">0</h6>
                    </div>
                    <div class="col-6">
                      <p class="mb-1">Total a Pagar:</p>
                      <h6 id="costo" class="text-success">$0.00</h6>
                    </div>
                  </div>
                  <input type="hidden" id="costo_total" name="costo_product[]" value="0">
                  <input type="hidden" id="cantidad_total" name="cantidad_product[]" value="0">
                </div>
                <div class="col-md-6 text-end">
                  <button type="submit" name="reg_orden" class="btn btn-orden" id="btnCrearOrden" disabled>
                    <i class="bi bi-check-circle me-2"></i>Crear Orden
                  </button>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div class="no-products">
              <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
              <h4 class="mt-3 text-muted">No hay productos disponibles</h4>
              <p class="text-muted">Este stand aún no tiene productos registrados.</p>
            </div>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

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

      // Mostrar los resultados
      document.getElementById('cantidad').innerText = cantidadTotal;
      document.getElementById('costo').innerText = '$' + costoTotal.toFixed(2);
      document.getElementById('cantidad_total').value = cantidadTotal;
      document.getElementById('costo_total').value = costoTotal.toFixed(2);

      // Habilitar/deshabilitar botón de crear orden
      const btnCrearOrden = document.getElementById('btnCrearOrden');
      if (cantidadTotal > 0) {
        btnCrearOrden.disabled = false;
        btnCrearOrden.classList.remove('btn-secondary');
        btnCrearOrden.classList.add('btn-orden');
      } else {
        btnCrearOrden.disabled = true;
        btnCrearOrden.classList.remove('btn-orden');
        btnCrearOrden.classList.add('btn-secondary');
      }
    }

    // Inicializar suma al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
      sumar_orden();
    });
  </script>
</body>
</html> 