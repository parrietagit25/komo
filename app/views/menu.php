      <!-- Menú lateral -->
      <div class="col-md-3 col-lg-2 sidebar">
        <div class="text-center mb-4">
          <img src="icons/logo.png" alt="Koomodo" width="40" />
          <h5 class="fw-bold mt-2">Koomodo</h5>
        </div>
        <a href="main" class="active"><i class="bi bi-person-circle me-2"></i>DashBoard</a>
        <?php if ($_SESSION['usuario']['tipo_usuario'] == 1) { ?>
        <a href="usuarios"><i class="bi bi-person-circle me-2"></i>Usuarios</a>
        <a href="stands"><i class="bi bi-building me-2"></i>Stands</a>
        <a href="evento"><i class="bi bi-calendar-event me-2"></i>Evento</a>
        <a href="wallet"><i class="bi bi-currency-dollar"></i>Wallet</a>
        <?php } ?>
        <?php if ($_SESSION['usuario']['tipo_usuario'] == 2) { ?>
          <a href="mis_productos"><i class="bi bi-building me-2"></i>Mis Productos</a>
          <a href="ordenes_directas"><i class="bi bi-cart-plus me-2"></i>Órdenes Directas</a>
        <?php } ?>
        <?php if ($_SESSION['usuario']['tipo_usuario'] == 3) { ?>
          <a href="escanear_qr"><i class="bi bi-qr-code me-2"></i>Escanear QR</a>
          <a href="mi_qr"><i class="bi bi-person-badge me-2"></i>Mi QR</a>
          <a href="mi_wallet"><i class="bi bi-wallet2 me-2"></i>Mi Wallet</a>
        <?php } ?>
        <hr class="text-secondary" />
        <a href="kooomo_eventos"><i class="bi bi-balloon-heart"></i>Koomo-Eventos</a>
        <hr class="text-secondary" />
        <a href="logout"><i class="bi bi-box-arrow-right me-2"></i>Salir</a>
      </div>