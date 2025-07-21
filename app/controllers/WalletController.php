<?php
require_once __DIR__ . '/../models/Wallet.php';
require_once __DIR__ . '/../models/Usuario.php';

class WalletController {
    private $walletModel;
    private $usuarioModel;

    public function __construct($db) {
        $this->walletModel = new Wallet($db);
        $this->usuarioModel = new Usuario($db);
    }

    public function index() {
        // Obtener todas las transacciones para admin
        $transacciones = $this->walletModel->obtenerTodasLasTransacciones();
        $usuarios = $this->usuarioModel->obtenerTodos();
        
        include __DIR__ . '/../views/wallet.php';
    }

    public function cargarDinero() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_POST['id_user'];
            $monto = $_POST['monto'];
            $descripcion = $_POST['descripcion'] ?? 'Carga manual';

            // Debug temporal
            error_log("Procesando carga de dinero: Usuario=$id_user, Monto=$monto, Desc=$descripcion");
            
            // Mostrar en pantalla también
            echo "<div style='background:yellow;padding:10px;margin:10px;'>";
            echo "DEBUG: Procesando carga de dinero<br>";
            echo "Usuario: $id_user<br>";
            echo "Monto: $monto<br>";
            echo "Descripción: $descripcion<br>";
            echo "</div>";

            if ($this->walletModel->cargarDinero($id_user, $monto, $descripcion)) {
                $_SESSION['success'] = "Se cargó exitosamente $" . number_format($monto, 2) . " al usuario.";
                echo "<div style='background:green;color:white;padding:10px;margin:10px;'>ÉXITO: Dinero cargado</div>";
            } else {
                $_SESSION['error'] = "Error al cargar el dinero.";
                echo "<div style='background:red;color:white;padding:10px;margin:10px;'>ERROR: No se pudo cargar el dinero</div>";
            }
        }
        
        // Redirigir de vuelta a la página de wallet
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'wallet');
        exit();
    }

    public function miWallet() {
        // Para usuarios normales, mostrar su saldo e historial
        $id_user = $_SESSION['usuario']['id'];
        $saldo = $this->walletModel->obtenerSaldoUsuario($id_user);
        $historial = $this->walletModel->obtenerHistorial($id_user);
        
        include __DIR__ . '/../views/mi_wallet.php';
    }
}
?>
