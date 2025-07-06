<?php
class Productos {
    private $conn;
    private $table = "stands";
    private $table2 = "usuarios";
    private $table3 = "productos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerProductosPorStand($id_stand) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table3} WHERE id_stand = :id_stand");
        $stmt->bindParam(':id_stand', $id_stand);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerStand($id_user) {
        $stmt = $this->conn->prepare("SELECT s.id, s.nombre, s.descripcion, s.ubicacion, s.estado, u.nombre_completo 
                                      FROM {$this->table} s INNER JOIN {$this->table2} u ON u.id = s.id_user
                                      WHERE 
                                      s.id_user=:id_user");

        $stmt->bindParam(':id_user', $id_user);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar_producto($id_user, $id_stand, $nombre_producto, $foto, $costo, $stat) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table3} (id_user, id_stand, nombre_producto, foto, costo, stat) VALUES (:id_user, :id_stand, :nombre_producto, :foto, :costo, :stat)");
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_stand', $id_stand);
        $stmt->bindParam(':nombre_producto', $nombre_producto);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':costo', $costo);
        $stmt->bindParam(':stat', $stat);
        return $stmt->execute();
    }

    public function actualizar_producto($id, $id_user, $id_stand, $nombre_producto, $foto, $costo, $stat) {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table3} 
            SET id_user = :id_user, 
                id_stand = :id_stand, 
                nombre_producto = :nombre_producto, 
                foto = :foto, 
                costo = :costo, 
                stat = :stat 
            WHERE id = :id
        ");
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_stand', $id_stand);
        $stmt->bindParam(':nombre_producto', $nombre_producto);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':costo', $costo);
        $stmt->bindParam(':stat', $stat);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function eliminar_producto($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table3} WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function guardar_producto(){
    
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
            $exito = $this->registrar_producto($id_user, $id_stand, $nombre_producto, $foto_path, $costo, $stat);

            if ($exito) {
                $_SESSION['success'] = 'Producto registrado exitosamente.';
            } else {
                $_SESSION['error'] = 'Error al registrar el producto.';
            }

            header('Location: mis_productos');
            exit;
        }

    }

}
