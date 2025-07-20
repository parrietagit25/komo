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

// Función para generar código QR
function generarQR($texto) {
    $url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($texto);
    return $url;
}

$user_id = $_SESSION['usuario']['id'];
$user_name = $_SESSION['usuario']['nombre_completo'];
$qr_data = "CLIENT_ID:" . $user_id;
$qr_url = generarQR($qr_data);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Koomodo - Mi QR</title>
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
    .qr-container {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      padding: 3rem;
      text-align: center;
      margin: 2rem auto;
      max-width: 600px;
      border: 2px solid rgba(40, 199, 111, 0.3);
    }
    .qr-code {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      margin: 2rem 0;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    .qr-code img {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
    }
    .user-info {
      background: rgba(40, 199, 111, 0.1);
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      border-left: 4px solid #28C76F;
    }
    .btn-download {
      background: linear-gradient(45deg, #28C76F, #24a85f);
      border: none;
      color: white;
      padding: 1rem 2rem;
      border-radius: 25px;
      font-weight: bold;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      margin: 0.5rem;
    }
    .btn-download:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(40, 199, 111, 0.4);
      color: white;
    }
    .btn-share {
      background: linear-gradient(45deg, #007bff, #0056b3);
      border: none;
      color: white;
      padding: 1rem 2rem;
      border-radius: 25px;
      font-weight: bold;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      margin: 0.5rem;
    }
    .btn-share:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
      color: white;
    }
    .qr-info {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 1.5rem;
      margin-top: 2rem;
    }
    .qr-info h6 {
      color: #28C76F;
      margin-bottom: 1rem;
    }
    .qr-info ul {
      text-align: left;
      margin-bottom: 0;
    }
    .qr-info li {
      margin-bottom: 0.5rem;
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
          <h3><i class="bi bi-person-badge me-2"></i>Mi Código QR</h3>
        </div>

        <div class="qr-container">
          <!-- Información del Usuario -->
          <div class="user-info">
            <h4 class="mb-3"><i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($user_name) ?></h4>
            <p class="mb-2">
              <i class="bi bi-shield-check me-2"></i>ID de Cliente: <strong><?= $user_id ?></strong>
            </p>
            <p class="mb-0">
              <i class="bi bi-calendar me-2"></i>Miembro desde: <strong><?= date('d/m/Y', strtotime($_SESSION['usuario']['creado'] ?? 'now')) ?></strong>
            </p>
          </div>

          <!-- Código QR -->
          <div class="qr-code">
            <h5 class="mb-3">Tu Código QR Personal</h5>
            <img src="<?= $qr_url ?>" alt="Mi QR Code" class="img-fluid">
            <p class="text-muted mt-3">Este código QR contiene tu ID de cliente para identificación</p>
          </div>

          <!-- Botones de Acción -->
          <div class="mt-4">
            <a href="<?= $qr_url ?>" download="mi_qr_<?= $user_id ?>.png" class="btn btn-download">
              <i class="bi bi-download me-2"></i>Descargar QR
            </a>
            <button onclick="compartirQR()" class="btn btn-share">
              <i class="bi bi-share me-2"></i>Compartir QR
            </button>
          </div>

          <!-- Información sobre el QR -->
          <div class="qr-info">
            <h6><i class="bi bi-info-circle me-2"></i>¿Para qué sirve mi código QR?</h6>
            <ul>
              <li><strong>Identificación rápida:</strong> Los stands pueden escanear tu QR para identificarte</li>
              <li><strong>Acceso directo:</strong> Facilita el proceso de compra en eventos</li>
              <li><strong>Historial personal:</strong> Mantiene registro de tus compras</li>
              <li><strong>Seguridad:</strong> Tu información está protegida y solo contiene tu ID</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    function compartirQR() {
      // Verificar si el navegador soporta la API de compartir
      if (navigator.share) {
        navigator.share({
          title: 'Mi Código QR - Koomodo',
          text: 'Este es mi código QR personal para identificación en eventos Koomodo',
          url: window.location.href
        }).catch(console.error);
      } else {
        // Fallback: copiar URL al portapapeles
        navigator.clipboard.writeText(window.location.href).then(() => {
          alert('URL copiada al portapapeles. Puedes compartirla manualmente.');
        }).catch(() => {
          alert('No se pudo copiar la URL. Puedes compartir la página manualmente.');
        });
      }
    }

    // Función para descargar el QR como imagen
    function descargarQR() {
      const link = document.createElement('a');
      link.href = '<?= $qr_url ?>';
      link.download = 'mi_qr_<?= $user_id ?>.png';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  </script>
</body>
</html> 