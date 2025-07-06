<?php
class Stand {
    private $conn;
    private $table = "stands";
    private $table2 = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodosStand() {
        $stmt = $this->conn->prepare("SELECT s.id, s.nombre, s.descripcion, s.ubicacion, s.estado, u.nombre_completo 
                                      FROM {$this->table} s INNER JOIN {$this->table2} u ON u.id = s.id_user ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($nombre, $ubicacion, $descripcion, $stat, $id_user) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nombre, ubicacion, estado, descripcion, id_user) VALUES (:nombre, :ubicacion, :descripcion, :estado, :id_user)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':ubicacion', $ubicacion);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':estado', $stat);
        $stmt->bindParam(':id_user', $id_user);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre, $ubicacion) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET nombre = :nombre, ubicacion = :ubicacion WHERE id = :id");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':ubicacion', $ubicacion);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
