<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/includes/session.php';

$db = new Database();
$conn = $db->connect();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Remover el directorio base si existe
$base_path = '/koomodo/public';
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}
$path = trim($uri, '/');

require_once __DIR__ . '/../app/controllers/UsuarioController.php';
require_once __DIR__ . '/../app/controllers/StandController.php'; 
require_once __DIR__ . '/../app/controllers/EventoController.php';

$model = new Usuario($conn); 

switch ($path) {
    case '':
    case 'index.php':
        // Si acceden directamente a public/ o index.php, redirigir a main
        header("Location: main");
        exit();
        break;
        
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
        // Mostrar página de wallet
        require_once __DIR__ . '/../app/controllers/WalletController.php';
        $walletController = new WalletController($conn);
        $walletController->index();
        break;
        
    case 'mi_wallet':
        require_once __DIR__ . '/../app/controllers/WalletController.php';
        $walletController = new WalletController($conn);
        $walletController->miWallet();
        break;
        
    case 'mis_productos':
        require_once __DIR__ . '/../app/views/mis_productos_stand.php';
        break;
        
    case 'ordenes_directas':
        require_once __DIR__ . '/../app/views/ordenes_directas.php';
        break;
        
    case 'asignar_orden':
        require_once __DIR__ . '/../app/views/asignar_orden.php';
        break;
        
    case 'registro_producto':
        require_once __DIR__ . '/../app/controllers/MisproductosController.php';
        break;
        
    case 'escanear_qr':
        require_once __DIR__ . '/../app/views/escanear_qr.php';
        break;
        
    case 'mi_qr':
        require_once __DIR__ . '/../app/views/mi_qr.php';
        break;
        
    case 'productos_stand':
        require_once __DIR__ . '/../app/views/productos_stand.php';
        break;
        
    case 'kooomo_eventos':
        require_once __DIR__ . '/../app/views/koomo_eventos.php';
        break;
        
    default:
        echo "404 - Página no encontrada: " . $path;
        break;
}
