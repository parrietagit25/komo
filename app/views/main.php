<?php
require_once __DIR__ . '/../includes/session.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login");
    exit();
}

// Cargar modelos necesarios
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Stand.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Wallet.php';
require_once __DIR__ . '/../models/OrdenDirecta.php';
require_once __DIR__ . '/../models/koomoeventos.php';

$db = new Database();
$conn = $db->connect();

$eventoModel = new Evento($conn);
$standModel = new Stand($conn);
$usuarioModel = new Usuario($conn);
$walletModel = new Wallet($conn);
$ordenDirectaModel = new OrdenDirecta($conn);
$koomoeventosModel = new Komodoeventos($conn);

// Obtener estadísticas según el tipo de usuario
$stats = [];

if ($_SESSION['usuario']['tipo_usuario'] == 1) {
    // Admin - Estadísticas generales
    $stats['total_eventos'] = $eventoModel->contarEventos();
    $stats['total_stands'] = $standModel->contarStands();
    $stats['total_clientes'] = $usuarioModel->contarUsuariosPorTipo(3); // tipo 3 = clientes
    $stats['total_transacciones'] = $walletModel->contarTransacciones();
    $stats['total_ventas_hoy'] = $walletModel->obtenerVentasHoy();
    $stats['total_ingresos_mes'] = $walletModel->obtenerIngresosMes();
} elseif ($_SESSION['usuario']['tipo_usuario'] == 2) {
    // Stand - Estadísticas del stand
    $user_id = $_SESSION['usuario']['id'];
    $stands_usuario = $standModel->obtenerStandsPorUsuario($user_id);
    $stats['saldo_acreditado'] = 0;
    $stats['total_pedidos'] = 0;
    $stats['pedidos_hoy'] = 0;
    $stats['ventas_mes'] = 0;
    
    foreach ($stands_usuario as $stand) {
        $saldo_stand = $walletModel->obtenerSaldoUsuario($stand['id']);
        $stats['saldo_acreditado'] += $saldo_stand;
        
        $pedidos_stand = $ordenDirectaModel->contarOrdenesPorStand($stand['id']);
        $stats['total_pedidos'] += $pedidos_stand;
        
        $pedidos_hoy = $ordenDirectaModel->contarOrdenesHoyPorStand($stand['id']);
        $stats['pedidos_hoy'] += $pedidos_hoy;
        
        $ventas_mes = $walletModel->obtenerVentasMesPorStand($stand['id']);
        $stats['ventas_mes'] += $ventas_mes;
    }
} elseif ($_SESSION['usuario']['tipo_usuario'] == 3) {
    // Cliente - Estadísticas del cliente
    $user_id = $_SESSION['usuario']['id'];
    $stats['saldo_disponible'] = $walletModel->obtenerSaldoUsuario($user_id);
    $stats['total_pedidos'] = $koomoeventosModel->contarOrdenesPorCliente($user_id);
    $stats['pedidos_preparacion'] = $koomoeventosModel->contarOrdenesEnPreparacion($user_id);
    $stats['gastos_mes'] = $walletModel->obtenerGastosMesCliente($user_id);
    $stats['ultima_compra'] = $koomoeventosModel->obtenerUltimaCompra($user_id);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Koomodo - Panel Principal</title>
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
    .stats-card {
      background: linear-gradient(45deg, #28C76F, #24a85f);
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      border: none;
      transition: transform 0.3s ease;
    }
    .stats-card:hover {
      transform: translateY(-5px);
    }
    .stats-icon {
      font-size: 2.5rem;
      opacity: 0.8;
    }
    .stats-value {
      font-size: 2rem;
      font-weight: bold;
    }
    .stats-label {
      font-size: 0.9rem;
      opacity: 0.9;
    }
    .welcome-section {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      border-left: 4px solid #28C76F;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">

        <?php include('menu.php'); ?>

      <!-- Contenido principal -->
      <div class="col-md-9 col-lg-10 content">
        
        <!-- Sección de bienvenida -->
        <div class="welcome-section">
          <h3><i class="bi bi-person-circle me-2"></i>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre_completo']); ?></h3>
          <p class="text-muted mb-0">
            <?php 
            $hora = date('H');
            if ($hora < 12) {
                echo "¡Buenos días!";
            } elseif ($hora < 18) {
                echo "¡Buenas tardes!";
            } else {
                echo "¡Buenas noches!";
            }
            ?> 
            Hoy es <?= date('d/m/Y') ?>
          </p>
        </div>

        <?php if ($_SESSION['usuario']['tipo_usuario'] == 1) { ?>
          <!-- Dashboard Admin -->
          <div class="row">
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value"><?= number_format($stats['total_eventos']) ?></div>
                    <div class="stats-label">Total de Eventos</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-calendar-event"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value"><?= number_format($stats['total_stands']) ?></div>
                    <div class="stats-label">Total de Stands</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-building"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value"><?= number_format($stats['total_clientes']) ?></div>
                    <div class="stats-label">Total de Clientes</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-people"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value"><?= number_format($stats['total_transacciones']) ?></div>
                    <div class="stats-label">Total Transacciones</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-currency-dollar"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row mt-3">
            <div class="col-md-6">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value">$<?= number_format($stats['total_ventas_hoy'], 2) ?></div>
                    <div class="stats-label">Ventas de Hoy</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-graph-up"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value">$<?= number_format($stats['total_ingresos_mes'], 2) ?></div>
                    <div class="stats-label">Ingresos del Mes</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-calendar-month"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        <?php } ?>
        
        <?php if ($_SESSION['usuario']['tipo_usuario'] == 2) { ?>
          <!-- Dashboard Stand -->
          <div class="row">
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value">$<?= number_format($stats['saldo_acreditado'], 2) ?></div>
                    <div class="stats-label">Saldo Acreditado</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-wallet2"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value"><?= number_format($stats['total_pedidos']) ?></div>
                    <div class="stats-label">Total Pedidos</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-cart-check"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value"><?= number_format($stats['pedidos_hoy']) ?></div>
                    <div class="stats-label">Pedidos Hoy</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-calendar-day"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value">$<?= number_format($stats['ventas_mes'], 2) ?></div>
                    <div class="stats-label">Ventas del Mes</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        <?php } ?>

        <?php if ($_SESSION['usuario']['tipo_usuario'] == 3) { ?>
          <!-- Dashboard Cliente -->
          <div class="row">
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value">$<?= number_format($stats['saldo_disponible'], 2) ?></div>
                    <div class="stats-label">Saldo Disponible</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-wallet2"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value"><?= number_format($stats['total_pedidos']) ?></div>
                    <div class="stats-label">Total Pedidos</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-cart-check"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value"><?= number_format($stats['pedidos_preparacion']) ?></div>
                    <div class="stats-label">En Preparación</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-clock"></i>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="stats-value">$<?= number_format($stats['gastos_mes'], 2) ?></div>
                    <div class="stats-label">Gastos del Mes</div>
                  </div>
                  <div class="stats-icon">
                    <i class="bi bi-cash-stack"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <?php if ($stats['ultima_compra']): ?>
          <div class="row mt-3">
            <div class="col-12">
              <div class="stats-card">
                <h6><i class="bi bi-clock-history me-2"></i>Última Compra</h6>
                <p class="mb-1">Fecha: <?= date('d/m/Y H:i', strtotime($stats['ultima_compra']['fecha_log'])) ?></p>
                <p class="mb-0">Total: $<?= number_format($stats['ultima_compra']['total'], 2) ?></p>
              </div>
            </div>
          </div>
          <?php endif; ?>
          
        <?php } ?>

      </div>
    </div>
  </div>
</body>
</html>
