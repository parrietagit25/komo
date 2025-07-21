<?php
class Wallet {
    private $conn;
    private $table = 'cash_wallet';
    private $table_saldo = 'saldo_usuarios';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Cargar dinero a un cliente (admin)
    public function cargarDinero($id_user, $monto, $descripcion = 'Carga manual') {
        try {
            error_log("Iniciando carga de dinero: Usuario=$id_user, Monto=$monto");
            
            // Verificar que el usuario existe
            $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE id = :id_user");
            $stmt->bindParam(':id_user', $id_user);
            $stmt->execute();
            if (!$stmt->fetch()) {
                error_log("Error: Usuario $id_user no existe en la tabla usuarios");
                return false;
            }
            error_log("Usuario $id_user verificado correctamente");
            
            $this->conn->beginTransaction();

            // Registrar la transacción
            $stmt = $this->conn->prepare("INSERT INTO {$this->table} (id_user, monto, tipo, descripcion) 
                                        VALUES (:id_user, :monto, 'carga', :descripcion)");
            $stmt->bindParam(':id_user', $id_user);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':descripcion', $descripcion);
            
            if (!$stmt->execute()) {
                error_log("Error al insertar en cash_wallet: " . implode(", ", $stmt->errorInfo()));
                throw new Exception("Error al insertar transacción");
            }
            error_log("Transacción registrada en cash_wallet");

            // Actualizar o crear saldo del usuario
            if (!$this->actualizarSaldoUsuario($id_user, $monto, 'suma')) {
                error_log("Error al actualizar saldo del usuario");
                throw new Exception("Error al actualizar saldo");
            }

            error_log("Saldo actualizado en saldo_usuarios");

            $this->conn->commit();
            error_log("Transacción completada exitosamente");
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error al cargar dinero: ' . $e->getMessage());
            return false;
        }
    }

    // Obtener saldo actual de un usuario
    public function obtenerSaldoUsuario($id_user) {
        $stmt = $this->conn->prepare("SELECT saldo_actual FROM {$this->table_saldo} WHERE id_user = :id_user");
        $stmt->bindParam(':id_user', $id_user);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['saldo_actual'] : 0.00;
    }

    // Actualizar saldo de usuario
    private function actualizarSaldoUsuario($id_user, $monto, $operacion = 'suma') {
        try {
            // Verificar si existe el registro
            $stmt = $this->conn->prepare("SELECT id FROM {$this->table_saldo} WHERE id_user = :id_user");
            $stmt->bindParam(':id_user', $id_user);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                // Actualizar saldo existente
                $signo = ($operacion == 'suma') ? '+' : '-';
                $stmt = $this->conn->prepare("UPDATE {$this->table_saldo} 
                                            SET saldo_actual = saldo_actual {$signo} :monto, 
                                                fecha_actualizacion = NOW() 
                                            WHERE id_user = :id_user");
                $stmt->bindParam(':id_user', $id_user);
                $stmt->bindParam(':monto', $monto);
                
                if (!$stmt->execute()) {
                    error_log("Error al actualizar saldo: " . implode(", ", $stmt->errorInfo()));
                    return false;
                }
                error_log("Saldo actualizado para usuario $id_user");
            } else {
                // Crear nuevo registro
                $saldo_inicial = ($operacion == 'suma') ? $monto : 0;
                $stmt = $this->conn->prepare("INSERT INTO {$this->table_saldo} (id_user, saldo_actual) 
                                            VALUES (:id_user, :saldo)");
                $stmt->bindParam(':id_user', $id_user);
                $stmt->bindParam(':saldo', $saldo_inicial);
                
                if (!$stmt->execute()) {
                    error_log("Error al crear saldo: " . implode(", ", $stmt->errorInfo()));
                    return false;
                }
                error_log("Nuevo saldo creado para usuario $id_user");
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error en actualizarSaldoUsuario: " . $e->getMessage());
            return false;
        }
    }

    // Procesar compra (descontar de cliente, acreditar a stand)
    public function procesarCompra($id_cliente, $id_stand, $monto_total, $id_orden) {
        try {
            $this->conn->beginTransaction();

            // Verificar saldo del cliente
            $saldo_cliente = $this->obtenerSaldoUsuario($id_cliente);
            if ($saldo_cliente < $monto_total) {
                throw new Exception('Saldo insuficiente');
            }

            // Descontar del cliente
            $stmt = $this->conn->prepare("INSERT INTO {$this->table} (id_user, monto, tipo, id_orden, id_stand, descripcion) 
                                        VALUES (:id_user, :monto, 'compra', :id_orden, :id_stand, 'Compra de productos')");
            $stmt->bindParam(':id_user', $id_cliente);
            $stmt->bindParam(':monto', $monto_total);
            $stmt->bindParam(':id_orden', $id_orden);
            $stmt->bindParam(':id_stand', $id_stand);
            $stmt->execute();

            // Acreditar al stand
            $stmt = $this->conn->prepare("INSERT INTO {$this->table} (id_user, monto, tipo, id_orden, id_stand, descripcion) 
                                        VALUES (:id_user, :monto, 'venta', :id_orden, :id_stand, 'Venta de productos')");
            $stmt->bindParam(':id_user', $id_stand);
            $stmt->bindParam(':monto', $monto_total);
            $stmt->bindParam(':id_orden', $id_orden);
            $stmt->bindParam(':id_stand', $id_stand);
            $stmt->execute();

            // Actualizar saldos
            $this->actualizarSaldoUsuario($id_cliente, $monto_total, 'resta');
            $this->actualizarSaldoUsuario($id_stand, $monto_total, 'suma');

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error al procesar compra: ' . $e->getMessage());
            return false;
        }
    }

    // Obtener historial de transacciones
    public function obtenerHistorial($id_user, $tipo = null) {
        $sql = "SELECT * FROM {$this->table} WHERE id_user = :id_user";
        if ($tipo) {
            $sql .= " AND tipo = :tipo";
        }
        $sql .= " ORDER BY fecha_log DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_user', $id_user);
        if ($tipo) {
            $stmt->bindParam(':tipo', $tipo);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todas las transacciones (admin)
    public function obtenerTodasLasTransacciones() {
        $stmt = $this->conn->prepare("SELECT cw.*, u.nombre_completo as nombre_usuario 
                                     FROM {$this->table} cw 
                                     LEFT JOIN usuarios u ON cw.id_user = u.id 
                                     ORDER BY cw.fecha_log DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Contar total de transacciones
    public function contarTransacciones() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Obtener ventas de hoy
    public function obtenerVentasHoy() {
        $stmt = $this->conn->prepare("SELECT SUM(monto) as total FROM {$this->table} 
                                     WHERE tipo = 'venta' AND DATE(fecha_log) = CURDATE()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Obtener ingresos del mes
    public function obtenerIngresosMes() {
        $stmt = $this->conn->prepare("SELECT SUM(monto) as total FROM {$this->table} 
                                     WHERE tipo = 'venta' AND MONTH(fecha_log) = MONTH(CURDATE()) 
                                     AND YEAR(fecha_log) = YEAR(CURDATE())");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Obtener ventas del mes por stand
    public function obtenerVentasMesPorStand($id_stand) {
        $stmt = $this->conn->prepare("SELECT SUM(monto) as total FROM {$this->table} 
                                     WHERE tipo = 'venta' AND id_stand = :id_stand 
                                     AND MONTH(fecha_log) = MONTH(CURDATE()) 
                                     AND YEAR(fecha_log) = YEAR(CURDATE())");
        $stmt->bindParam(':id_stand', $id_stand);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Obtener gastos del mes del cliente
    public function obtenerGastosMesCliente($id_cliente) {
        $stmt = $this->conn->prepare("SELECT SUM(monto) as total FROM {$this->table} 
                                     WHERE tipo = 'compra' AND id_user = :id_cliente 
                                     AND MONTH(fecha_log) = MONTH(CURDATE()) 
                                     AND YEAR(fecha_log) = YEAR(CURDATE())");
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
?>
