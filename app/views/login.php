<!-- Ruta: koomodo-pwa/app/views/login.php -->

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Koomodo - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet" />
  <meta name="theme-color" content="#0B1C3D" />
  <link rel="manifest" href="/manifest.json" />
  <style>
    body {
      background-color: #0B1C3D;
      color: white;
    }
    .login-card {
      max-width: 400px;
      width: 100%;
      background-color: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      padding: 2rem;
    }
    .form-control.bg-dark {
      background-color: #1e1e2f !important;
      color: white !important;
      border: 1px solid #6c757d !important;
    }
    .btn-success {
      background-color: #28C76F;
      font-weight: bold;
    }
    .btn-outline-warning {
      border-color: #ffc107;
      color: #ffc107;
    }
  </style>
</head>
<body>
  <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card login-card shadow-lg text-white">
      <div class="text-center mb-4">
        <img src="icons/logo.png" alt="Koomodo" width="50" />
        <h2 class="mt-2 fw-bold">Koomodo</h2>
      </div>

      <h5 class="text-center mb-1 fw-bold">Bienvenido de nuevo</h5>
      <p class="text-center text-white-50 mb-4">Inicia sesión en tu cuenta</p>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center py-1 mb-3">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger">Credenciales incorrectas. Intente de nuevo.</div>
      <?php endif; ?>
      <form method="post" action="login">
        <input
          type="text"
          name="usuario"
          placeholder="Correo o usuario"
          class="form-control mb-3 bg-dark"
          required
        />
        <input
          type="password"
          name="password"
          placeholder="Contraseña"
          class="form-control mb-3 bg-dark"
          required
        />
        <button type="submit" class="btn btn-success w-100 mb-3">
          Iniciar sesión
        </button>
      </form>

      <button class="btn btn-outline-light w-100 mb-2">
        📧 Iniciar sesión con correo
      </button>
      <button class="btn btn-outline-primary w-100 mb-2">
        🌐 Iniciar sesión con Facebook
      </button>
      <button class="btn btn-outline-warning w-100 mb-3">
        🔒 Huella digital / reconocimiento facial
      </button>

      <div class="d-flex justify-content-between mt-2">
        <a href="#" class="text-white-50 text-decoration-none">¿Olvidaste tu contraseña?</a>
        <a href="#" class="text-success fw-bold text-decoration-none">Regístrate</a>
      </div>
    </div>
  </div>
</body>
</html>
