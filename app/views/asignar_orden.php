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

// Obtener el ID de la orden desde la URL
$id_orden = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_orden <= 0) {
    header("Location: ordenes_directas");
    exit();
}

// Obtener información de la orden
$orden = $ordenDirectaModel->obtenerOrdenDirecta($id_orden);
if (!$orden) {
    header("Location: ordenes_directas");
    exit();
}

// Procesar asignación de cliente mediante QR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_cliente'])) {
    $id_cliente = (int)($_POST['id_cliente'] ?? 0);
    
    if ($id_cliente > 0) {
        $resultado = $ordenDirectaModel->asignarCliente($id_orden, $id_cliente);
        if ($resultado) {
            $_SESSION['success'] = "¡Cliente asignado exitosamente a la orden #$id_orden!";
            header("Location: ordenes_directas");
            exit();
        } else {
            $_SESSION['error'] = "Error al asignar el cliente a la orden.";
        }
    } else {
        $_SESSION['error'] = "ID de cliente inválido.";
    }
}

// Obtener detalles de la orden
$detalles = $ordenDirectaModel->obtenerDetalleOrdenDirecta($id_orden);
$total_orden = $ordenDirectaModel->obtenerTotalOrden($id_orden);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Koomodo - Asignar Cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <script src="https://unpkg.com/html5-qrcode"></script>
  
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
    .order-info {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      border-left: 4px solid #28C76F;
    }
    .scanner-container {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 2rem;
      text-align: center;
      margin: 2rem auto;
      max-width: 600px;
      border: 2px solid rgba(40, 199, 111, 0.3);
    }
    .camera-container {
      border: 2px solid #28C76F;
      border-radius: 10px;
      overflow: hidden;
      margin: 1rem 0;
    }
    .btn-scan {
      background: linear-gradient(45deg, #28C76F, #24a85f);
      border: none;
      color: white;
      padding: 12px 30px;
      border-radius: 25px;
      font-weight: bold;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }
    .btn-scan:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(40, 199, 111, 0.4);
    }
    .status-message {
      margin-top: 1rem;
      padding: 1rem;
      border-radius: 8px;
    }
    .status-success {
      background-color: rgba(40, 199, 111, 0.2);
      border: 1px solid #28C76F;
    }
    .status-error {
      background-color: rgba(220, 53, 69, 0.2);
      border: 1px solid #dc3545;
    }
    .qr-overlay {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 200px;
      height: 200px;
      border: 2px solid #28C76F;
      border-radius: 10px;
      pointer-events: none;
    }
    .qr-overlay::before {
      content: '';
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      border: 2px solid transparent;
      border-radius: 10px;
      background: linear-gradient(45deg, #28C76F, transparent, #28C76F);
      animation: scan 2s linear infinite;
    }
    @keyframes scan {
      0% { transform: translateY(-100%); }
      100% { transform: translateY(100%); }
    }
    .details-table {
      background: rgba(255, 255, 255, 0.03);
      border-radius: 10px;
      padding: 1rem;
      margin-top: 1rem;
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
            <h3><i class="bi bi-person-plus me-2"></i>Asignar Cliente</h3>
          </div>
        </div>

        <!-- Información de la Orden -->
        <div class="order-info">
          <h4 class="mb-3">Orden #<?= $orden['id'] ?></h4>
          <p class="mb-2"><strong>Descripción:</strong> <?= htmlspecialchars($orden['nombre']) ?></p>
          <p class="mb-2"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha_log'])) ?></p>
          <p class="mb-0"><strong>Total:</strong> $<?= number_format($total_orden, 2) ?></p>
          
          <!-- Detalles de la orden -->
          <div class="details-table">
            <h6 class="mb-3">Productos de la Orden:</h6>
            <div class="table-responsive">
              <table class="table table-sm text-white">
                <thead class="table-secondary text-dark">
                  <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Monto</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($detalles as $detalle): ?>
                    <tr>
                      <td><?= htmlspecialchars($detalle['nombre_producto']) ?></td>
                      <td><?= $detalle['cantidad'] ?></td>
                      <td>$<?= number_format($detalle['monto'], 2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Escáner QR -->
        <div class="scanner-container">
          <h4 class="mb-4">Escanea el QR del Cliente</h4>
          <p class="text-muted mb-4">Coloca el código QR del cliente frente a la cámara para asignar la orden</p>
          
          <div class="camera-container">
            <div id="reader" style="width: 100%; height: 400px; position: relative;">
              <div class="qr-overlay"></div>
            </div>
          </div>

          <div id="status-message" class="status-message" style="display: none;"></div>

          <div class="mt-4">
            <button id="start-scan" class="btn btn-scan text-white me-2">
              <i class="bi bi-camera me-2"></i>Iniciar Escaneo
            </button>
            <button id="stop-scan" class="btn btn-outline-light" style="display: none;">
              <i class="bi bi-stop-circle me-2"></i>Detener Escaneo
            </button>
          </div>

          <div class="mt-4">
            <button id="manual-input" class="btn btn-outline-info">
              <i class="bi bi-keyboard me-2"></i>Ingresar ID Manualmente
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para entrada manual -->
  <div class="modal fade" id="manualModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white">
        <div class="modal-header">
          <h5 class="modal-title">Ingresar ID del Cliente</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="post" action="">
            <label for="id_cliente" class="form-label">ID del Cliente:</label>
            <input type="number" id="id_cliente" name="id_cliente" class="form-control bg-dark text-white" 
                   placeholder="Ej: 1, 2, 3..." required>
            <div class="mt-3">
              <button type="submit" name="asignar_cliente" class="btn btn-success">
                <i class="bi bi-check-circle me-2"></i>Asignar Cliente
              </button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    let html5QrcodeScanner = null;
    let isScanning = false;

    // Función para mostrar mensaje de estado
    function showStatus(message, isSuccess = true) {
      const statusDiv = document.getElementById('status-message');
      statusDiv.className = `status-message ${isSuccess ? 'status-success' : 'status-error'}`;
      statusDiv.innerHTML = message;
      statusDiv.style.display = 'block';
      
      setTimeout(() => {
        statusDiv.style.display = 'none';
      }, 5000);
    }

    // Función para procesar el resultado del QR
    function processQRResult(decodedText) {
      console.log('QR detectado:', decodedText);
      
      // Verificar si el QR contiene el formato esperado
      if (decodedText.startsWith('CLIENT_ID:')) {
        const clientId = decodedText.replace('CLIENT_ID:', '');
        showStatus(`¡Cliente detectado! ID: ${clientId}`, true);
        
        // Asignar cliente a la orden
        setTimeout(() => {
          document.getElementById('id_cliente').value = clientId;
          const modal = new bootstrap.Modal(document.getElementById('manualModal'));
          modal.show();
        }, 2000);
      } else {
        showStatus('Código QR no válido. Debe ser un código de cliente.', false);
      }
    }

    // Función para iniciar el escaneo
    function startScanning() {
      if (isScanning) return;
      
      html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { 
          fps: 10, 
          qrbox: { width: 250, height: 250 },
          aspectRatio: 1.0
        },
        false
      );

      html5QrcodeScanner.render((decodedText, decodedResult) => {
        html5QrcodeScanner.clear();
        processQRResult(decodedText);
      }, (errorMessage) => {
        // Manejar errores silenciosamente
      });

      isScanning = true;
      document.getElementById('start-scan').style.display = 'none';
      document.getElementById('stop-scan').style.display = 'inline-block';
    }

    // Función para detener el escaneo
    function stopScanning() {
      if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
        html5QrcodeScanner = null;
      }
      isScanning = false;
      document.getElementById('start-scan').style.display = 'inline-block';
      document.getElementById('stop-scan').style.display = 'none';
    }

    // Event listeners
    document.getElementById('start-scan').addEventListener('click', startScanning);
    document.getElementById('stop-scan').addEventListener('click', stopScanning);
    
    document.getElementById('manual-input').addEventListener('click', () => {
      const modal = new bootstrap.Modal(document.getElementById('manualModal'));
      modal.show();
    });

    // Iniciar escaneo automáticamente al cargar la página
    window.addEventListener('load', () => {
      setTimeout(startScanning, 1000);
    });
  </script>
</body>
</html> 