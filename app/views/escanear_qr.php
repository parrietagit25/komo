<?php
require_once __DIR__ . '/../includes/session.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login");
    exit();
}

// Verificar que sea usuario cliente
if ($_SESSION['usuario']['tipo_usuario'] != 3) {
    header("Location: main");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Koomodo - Escanear QR</title>
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
    .scanner-container {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 2rem;
      text-align: center;
      margin: 2rem auto;
      max-width: 600px;
    }
    .camera-container {
      border: 2px solid #28C76F;
      border-radius: 10px;
      overflow: hidden;
      margin: 1rem 0;
    }
    .btn-scan {
      background-color: #28C76F;
      border: none;
      padding: 12px 30px;
      border-radius: 25px;
      font-weight: bold;
      font-size: 1.1rem;
    }
    .btn-scan:hover {
      background-color: #24a85f;
      transform: scale(1.05);
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
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      
      <?php include 'menu.php'; ?>

      <!-- Área de contenido -->
      <div class="col-md-9 col-lg-10 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3><i class="bi bi-qr-code me-2"></i>Escanear Código QR</h3>
        </div>

        <div class="scanner-container">
          <h4 class="mb-4">Escanea el código QR del stand</h4>
          <p class="text-muted mb-4">Coloca el código QR del stand frente a la cámara para ver sus productos</p>
          
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
          <h5 class="modal-title">Ingresar ID del Stand</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="stand-id" class="form-label">ID del Stand:</label>
          <input type="number" id="stand-id" class="form-control bg-dark text-white" placeholder="Ej: 1, 2, 3...">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="confirm-manual">Confirmar</button>
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
      if (decodedText.startsWith('STAND_ID:')) {
        const standId = decodedText.replace('STAND_ID:', '');
        showStatus(`¡Stand detectado! ID: ${standId}`, true);
        
        // Redirigir a la página de productos del stand
        setTimeout(() => {
          window.location.href = `productos_stand?id=${standId}`;
        }, 2000);
      } else {
        showStatus('Código QR no válido. Debe ser un código de stand.', false);
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

    document.getElementById('confirm-manual').addEventListener('click', () => {
      const standId = document.getElementById('stand-id').value.trim();
      if (standId) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('manualModal'));
        modal.hide();
        showStatus(`Redirigiendo al stand ID: ${standId}`, true);
        setTimeout(() => {
          window.location.href = `productos_stand?id=${standId}`;
        }, 1000);
      } else {
        showStatus('Por favor ingresa un ID válido', false);
      }
    });

    // Iniciar escaneo automáticamente al cargar la página
    window.addEventListener('load', () => {
      setTimeout(startScanning, 1000);
    });
  </script>
</body>
</html> 