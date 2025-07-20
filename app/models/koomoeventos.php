<?php
class Komodoeventos {
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

    public function obtenerStand() {
        $stmt = $this->conn->prepare("SELECT s.id, s.nombre, s.descripcion, s.ubicacion, s.estado, u.nombre_completo 
                                      FROM {$this->table} s INNER JOIN {$this->table2} u ON u.id = s.id_user");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardar_orden() {
        
        try {
            $id_user = $_SESSION['usuario']['id'] ?? null;
            $id_stand = $_POST['id_stand'] ?? null;
            $cantidad_total = $_POST['cantidad_product'][0] ?? 0;
            $costo_total = $_POST['costo_product'][0] ?? 0;
            $id_evento = 1; // Puedes ajustar este valor si lo manejas dinámicamente

            $productos = $_POST['id_produc'] ?? [];
            $cantidades = $_POST['cantidad_pro'] ?? [];
            $montos = $_POST['product_costo'] ?? [];

            if (!$id_user || !$id_stand || count($productos) === 0) {
                $_SESSION['error'] = 'Datos incompletos para crear la orden.';
                return false;
            }

            // Verificar que al menos un producto tenga cantidad > 0
            $tieneProductos = false;
            foreach ($cantidades as $cantidad) {
                if ($cantidad > 0) {
                    $tieneProductos = true;
                    break;
                }
            }

            if (!$tieneProductos) {
                $_SESSION['error'] = 'Debe seleccionar al menos un producto.';
                return false;
            }

            // Iniciar transacción
            $this->conn->beginTransaction();

            // Insertar encabezado
            $stmt = $this->conn->prepare("INSERT INTO orden (id_user, id_stand, id_evento, monto_total, cantidad_productos, stat)
                                        VALUES (:id_user, :id_stand, :id_evento, :monto_total, :cantidad_productos, 1)");
            $stmt->bindParam(':id_user', $id_user);
            $stmt->bindParam(':id_stand', $id_stand);
            $stmt->bindParam(':id_evento', $id_evento);
            $stmt->bindParam(':monto_total', $costo_total);
            $stmt->bindParam(':cantidad_productos', $cantidad_total);
            $stmt->execute();

            $id_orden = $this->conn->lastInsertId();

            // Insertar detalle
            $stmtDetalle = $this->conn->prepare("INSERT INTO orden_detalle (id_orden, id_producto, cantidad, monto)
                                                VALUES (:id_orden, :id_producto, :cantidad, :monto)");

            for ($i = 0; $i < count($productos); $i++) {
                $id_producto = $productos[$i];
                $cantidad = $cantidades[$i];
                $monto = $montos[$i];

                if ($cantidad > 0) {
                    $stmtDetalle->bindParam(':id_orden', $id_orden);
                    $stmtDetalle->bindParam(':id_producto', $id_producto);
                    $stmtDetalle->bindParam(':cantidad', $cantidad);
                    $stmtDetalle->bindParam(':monto', $monto);
                    $stmtDetalle->execute();
                }
            }

            // Confirmar transacción
            $this->conn->commit();
            
            $_SESSION['success'] = '¡Orden creada exitosamente! Orden #' . $id_orden;
            return true;

        } catch (Exception $e) {
            // Si ocurre un error, revertir
            $this->conn->rollBack();
            error_log('Error al guardar la orden: ' . $e->getMessage());
            $_SESSION['error'] = 'Error al crear la orden. Intente nuevamente.';
            return false;
        }
    }


    public function obtener_ordenes($id_stand, $id_usuario) {
        $stmt = $this->conn->prepare("
            SELECT o.id, o.monto_total, o.cantidad_productos, o.fecha_log
            FROM orden o
            WHERE o.id_stand = :id_stand AND o.id_user = :id_usuario
            ORDER BY o.fecha_log DESC
        ");
        $stmt->bindParam(':id_stand', $id_stand);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener_detalle_orden($id_orden) {
        $stmt = $this->conn->prepare("
            SELECT p.nombre_producto, od.cantidad, od.monto
            FROM orden_detalle od
            JOIN productos p ON p.id = od.id_producto
            WHERE od.id_orden = :id_orden
        ");
        $stmt->bindParam(':id_orden', $id_orden);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
