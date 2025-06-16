<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = basename($uri);

switch ($path) {
    case '':
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../app/controllers/AuthController.php';
        } else {
            require_once __DIR__ . '/../app/views/login.php';
        }
        break;

    case 'main':
        require_once __DIR__ . '/../app/views/main.php';
        break;

    case 'logout':
        require_once __DIR__ . '/../app/includes/session.php';
        session_destroy();
        header("Location: login");
        break;

    case 'usuarios':
        require_once __DIR__ . '/../app/views/usuarios.php';
        break;

    case 'evento':
        require_once __DIR__ . '/../app/views/eventos.php';
        break;

    case 'stands':
        require_once __DIR__ . '/../app/views/stands.php';
        break;

    default:
        echo "404 - Página no encontrada";
        break;
}
