<?php
// Procesar formulario de carga de dinero si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wallet_cargar'])) {
    require_once __DIR__ . '/../models/Wallet.php';
    require_once __DIR__ . '/../../config/database.php';
    
    $db = new Database();
    $conn = $db->connect();
    $walletModel = new Wallet($conn);
    
    $id_user = $_POST['id_user'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'] ?? 'Carga manual';

    if ($walletModel->cargarDinero($id_user, $monto, $descripcion)) {
        $_SESSION['success'] = "Se cargó exitosamente $" . number_format($monto, 2) . " al usuario.";
    } else {
        $_SESSION['error'] = "Error al cargar el dinero.";
    }
    
    // Redirigir para evitar reenvío del formulario
    header("Location: wallet");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet - Koomodo</title>
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
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .transaction-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-cargar {
            background: linear-gradient(45deg, #28C76F, #24a85f);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-cargar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(40, 199, 111, 0.4);
        }
        .tipo-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .tipo-carga { background-color: #28C76F; }
        .tipo-compra { background-color: #dc3545; }
        .tipo-venta { background-color: #007bff; }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>

            <div class="col-md-9 col-lg-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="bi bi-wallet2 me-2"></i>Gestión de Wallet</h3>
                </div>

                <!-- Mensajes -->
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

                <!-- Formulario para cargar dinero -->
                <div class="form-container">
                    <h4 class="mb-4"><i class="bi bi-plus-circle me-2"></i>Cargar Dinero a Usuario</h4>
                    
                    <form method="post" action="">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Seleccionar Usuario</label>
                                <select name="id_user" class="form-select bg-dark text-white" required>
                                    <option value="">Seleccionar usuario...</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?= $usuario['id'] ?>">
                                            <?= htmlspecialchars($usuario['nombre_completo']) ?> 
                                            (<?= $usuario['tipo_usuario'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Monto a Cargar</label>
                                <input type="number" name="monto" class="form-control bg-dark text-white" 
                                       placeholder="0.00" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Descripción</label>
                                <input type="text" name="descripcion" class="form-control bg-dark text-white" 
                                       placeholder="Carga manual" value="Carga manual">
                            </div>
                        </div>
                        <div class="mt-3">
                            <input type="hidden" name="wallet_cargar" value="1">
                            <button type="submit" class="btn btn-cargar">
                                <i class="bi bi-plus-circle me-2"></i>Cargar Dinero
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Historial de Transacciones -->
                <div class="mt-5">
                    <h4 class="mb-4"><i class="bi bi-clock-history me-2"></i>Historial de Transacciones</h4>
                    
                    <?php if (!empty($transacciones)): ?>
                        <?php foreach ($transacciones as $transaccion): ?>
                            <div class="transaction-card">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <h6 class="mb-1"><?= htmlspecialchars($transaccion['nombre_usuario']) ?></h6>
                                        <small class="text-muted">ID: <?= $transaccion['id_user'] ?></small>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="tipo-badge tipo-<?= $transaccion['tipo'] ?>">
                                            <?= ucfirst($transaccion['tipo']) ?>
                                        </span>
                                    </div>
                                    <div class="col-md-2">
                                        <strong class="<?= $transaccion['tipo'] == 'compra' ? 'text-danger' : 'text-success' ?>">
                                            $<?= number_format($transaccion['monto'], 2) ?>
                                        </strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($transaccion['fecha_log'])) ?>
                                        </small>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted">
                                            <?= htmlspecialchars($transaccion['descripcion']) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>No hay transacciones registradas.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
