<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = basename($uri);

require_once __DIR__ . '/../app/controllers/UsuarioController.php';
require_once __DIR__ . '/../app/controllers/StandController.php'; 
require_once __DIR__ . '/../app/controllers/EventoController.php';

$model = new Usuario($conn); 

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
        $usuarios = $model->todos_usuarios();
        require_once __DIR__ . '/../app/views/usuarios.php';
        break;

    case 'evento':
        require_once __DIR__ . '/../app/views/eventos.php';
        break;

    case 'stands':
        $usuarios = $model->todos_usuarios_stand();
        require_once __DIR__ . '/../app/views/stands.php';
        break;

    case 'wallet':
        require_once __DIR__ . '/../app/views/wallet.php';
        break;
    case 'mis_productos':
        require_once __DIR__ . '/../app/views/mis_productos_stand.php';
        break;
    case 'registro_producto':
        require_once __DIR__ . '/../app/controllers/MisproductosController.php';
        break;
    case 'kooomo_eventos':
        require_once __DIR__ . '/../app/views/koomo_eventos.php';
        break;
    default:
        echo "404 - Página no encontrada";
        break;
}
