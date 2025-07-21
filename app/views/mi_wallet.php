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
      font-family: 'Segoe UI', Arial, sans-serif;
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
    .saldo-card {
      background: linear-gradient(90deg, #28C76F 0%, #1eae60 100%);
      border-radius: 25px;
      padding: 2.5rem 2rem;
      text-align: center;
      margin: 2rem auto 2rem auto;
      max-width: 500px;
      box-shadow: 0 8px 32px 0 rgba(40,199,111,0.15);
      color: #fff;
      position: relative;
      overflow: hidden;
    }
    .saldo-card h4 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }
    .saldo-card .bi-cash-coin {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
      color: #fff;
    }
    .saldo-card h2 {
      font-size: 3rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
      color: #fff;
      letter-spacing: 1px;
    }
    .saldo-card small {
      color: #e0ffe7;
    }

    .table-dark-custom {
      background: rgba(255,255,255,0.03);
      border-radius: 15px;
      overflow: hidden;
      color: #fff;
    }
    .table-dark-custom th, .table-dark-custom td {
      border: none;
      vertical-align: middle;
    }
    .table-dark-custom tbody tr {
      transition: background 0.2s;
    }
    .table-dark-custom tbody tr:hover {
      background: rgba(40,199,111,0.08);
    }
    .tipo-badge {
      display: inline-block;
      padding: 0.35em 1em;
      border-radius: 12px;
      font-size: 0.95em;
      font-weight: 600;
      letter-spacing: 0.03em;
      background: #222a;
      color: #fff;
      border: 1px solid transparent;
      transition: background 0.2s, color 0.2s;
    }
    .tipo-recarga {
      background: #28C76F;
      color: #fff;
    }
    .tipo-compra {
      background: #ff4d4f;
      color: #fff;
    }
    .tipo-bonificacion {
      background: #007bff;
      color: #fff;
    }
    @media (max-width: 768px) {
      .saldo-card {
        padding: 1.5rem 1rem;
        max-width: 100%;
      }
      .main-content {
        padding: 1rem !important;
      }
      .table-responsive {
        font-size: 0.95rem;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      
      <?php include 'menu.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="bi bi-wallet2 me-2"></i>Mi Wallet</h3>
                </div>

                <!-- Saldo Actual -->
                <div class="saldo-card">
                    <h4 class="mb-3"><i class="bi bi-cash-coin me-2"></i>Saldo Actual</h4>
                    <h2 class="display-4 mb-0">$<?= number_format($saldo, 2) ?></h2>
                    <small class="text-light">Última actualización: <?= date('d/m/Y H:i') ?></small>
                </div>

                <!-- Historial de Transacciones -->
                <div class="mt-5">
                    <h4 class="mb-4"><i class="bi bi-clock-history me-2"></i>Historial de Transacciones</h4>
                    
                    <?php if (!empty($historial)): ?>
                        <div class="table-responsive">
                            <table class="table table-dark-custom">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($historial as $transaccion): ?>
                                    <tr>
                                        <td><span class="tipo-badge tipo-<?= $transaccion['tipo'] ?>"><?= ucfirst($transaccion['tipo']) ?></span></td>
                                        <td class="<?= $transaccion['tipo'] == 'compra' ? 'text-danger' : 'text-success' ?>">
                                            $<?= number_format($transaccion['monto'], 2) ?>
                                        </td>
                                        <td>
                                            <small class="text-badge">
                                                <?= date('d/m/Y H:i', strtotime($transaccion['fecha_log'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-badge">
                                                <?= htmlspecialchars($transaccion['descripcion']) ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>No tienes transacciones registradas.
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 