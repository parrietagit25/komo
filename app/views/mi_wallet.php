<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Wallet - Koomodo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            color: white;
        }
        .content {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin: 1rem;
        }
        .saldo-card {
            background: linear-gradient(45deg, #28C76F, #24a85f);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .transaction-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
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
                    <h3><i class="bi bi-wallet2 me-2"></i>Mi Wallet</h3>
                </div>

                <!-- Saldo Actual -->
                <div class="saldo-card">
                    <h4 class="mb-3"><i class="bi bi-cash-coin me-2"></i>Saldo Actual</h4>
                    <h2 class="display-4 mb-0">$<?= number_format($saldo, 2) ?></h2>
                    <small class="text-white-50">Última actualización: <?= date('d/m/Y H:i') ?></small>
                </div>

                <!-- Historial de Transacciones -->
                <div class="mt-5">
                    <h4 class="mb-4"><i class="bi bi-clock-history me-2"></i>Historial de Transacciones</h4>
                    
                    <?php if (!empty($historial)): ?>
                        <?php foreach ($historial as $transaccion): ?>
                            <div class="transaction-card">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <span class="tipo-badge tipo-<?= $transaccion['tipo'] ?>">
                                            <?= ucfirst($transaccion['tipo']) ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong class="<?= $transaccion['tipo'] == 'compra' ? 'text-danger' : 'text-success' ?>">
                                            $<?= number_format($transaccion['monto'], 2) ?>
                                        </strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($transaccion['fecha_log'])) ?>
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">
                                            <?= htmlspecialchars($transaccion['descripcion']) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>No tienes transacciones registradas.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 