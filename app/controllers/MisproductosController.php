<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Productos.php';

$db    = new Database();
$conn  = $db->connect();
$model = new Productos($conn); 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_producto'])) {
    $id_user          = $_POST['id_user'] ?? null;
    $id_stand         = $_POST['id_stand'] ?? null;
    $nombre_producto  = trim($_POST['nombre_producto'] ?? '');
    $costo            = floatval($_POST['costo'] ?? 0);
    $stat             = $_POST['stat'] ?? 'Inactivo';

    // Validación mínima
    if (!$id_user || !$id_stand || $nombre_producto === '' || $costo <= 0) {
        $_SESSION['error'] = 'Datos incompletos para registrar el producto.';
        header('Location: mis_productos');
        exit;
    }

    // Procesamiento de imagen
    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombre_tmp   = $_FILES['foto']['tmp_name'];
        $nombre_arch  = basename($_FILES['foto']['name']);
        $ext          = strtolower(pathinfo($nombre_arch, PATHINFO_EXTENSION));

        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $permitidas)) {
            $nombre_final = uniqid('img_') . '.' . $ext;
            $ruta_destino = __DIR__ . '/../uploads/' . $nombre_final;

            if (!is_dir(__DIR__ . '/../uploads')) {
                mkdir(__DIR__ . '/../uploads', 0777, true);
            }

            if (move_uploaded_file($nombre_tmp, $ruta_destino)) {
                $foto_path = 'uploads/' . $nombre_final;
            }
        }
    }

    // Guardar en la base de datos
    $exito = $model->registrar_producto($id_user, $id_stand, $nombre_producto, $foto_path, $costo, $stat);

    if ($exito) {
        $_SESSION['success'] = 'Producto registrado exitosamente.';
    } else {
        $_SESSION['error'] = 'Error al registrar el producto.';
    }

    header('Location: mis_productos');
    exit;
} else {
    header('Location: mis_productos');
    exit;
}
