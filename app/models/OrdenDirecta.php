<?php
class OrdenDirecta {
    private $conn;
    private $table = "orden_directa";
    private $table2 = "orden_directa_detalle";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crearOrdenDirecta($nombre, $id_cliente, $id_stand) {
        try {
            $this->conn->beginTransaction();

            // Insertar encabezado de la orden directa
            $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nombre, id_cliente, id_stand, stat) 
                                        VALUES (:nombre, :id_cliente, :id_stand, 1)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->bindParam(':id_stand', $id_stand);
            $stmt->execute();

            $id_orden = $this->conn->lastInsertId();
            $this->conn->commit();
            
            return $id_orden;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error al crear orden directa: ' . $e->getMessage());
            return false;
        }
    }

    public function agregarDetalleOrden($id_orden, $nombre_producto, $cantidad, $monto) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO {$this->table2} (nombre_producto, cantidad, monto, id_orden_directa) 
                                        VALUES (:nombre_producto, :cantidad, :monto, :id_orden_directa)");
            $stmt->bindParam(':nombre_producto', $nombre_producto);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':id_orden_directa', $id_orden);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error al agregar detalle de orden directa: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerOrdenDirecta($id_orden) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id_orden);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function asignarCliente($id_orden, $id_cliente) {
        try {
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET id_cliente = :id_cliente WHERE id = :id");
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->bindParam(':id', $id_orden);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error al asignar cliente: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerOrdenesDirectas($id_stand) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_stand = :id_stand ORDER BY fecha_log DESC");
        $stmt->bindParam(':id_stand', $id_stand);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleOrdenDirecta($id_orden) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table2} WHERE id_orden_directa = :id_orden");
        $stmt->bindParam(':id_orden', $id_orden);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstadoOrden($id_orden, $estado) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET stat = :estado WHERE id = :id");
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id_orden);
        return $stmt->execute();
    }

    public function obtenerTotalOrden($id_orden) {
        $stmt = $this->conn->prepare("SELECT SUM(monto) as total FROM {$this->table2} WHERE id_orden_directa = :id_orden");
        $stmt->bindParam(':id_orden', $id_orden);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function obtenerClientePorId($id_cliente) {
        $stmt = $this->conn->prepare("SELECT nombre_completo FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id_cliente);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['nombre_completo'] ?? 'Cliente no encontrado';
    }

    public function contarOrdenesPorStand($id_stand) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE id_stand = :id_stand");
        $stmt->bindParam(':id_stand', $id_stand);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function contarOrdenesHoyPorStand($id_stand) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM {$this->table} 
                                     WHERE id_stand = :id_stand AND DATE(fecha_log) = CURDATE()");
        $stmt->bindParam(':id_stand', $id_stand);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
} 