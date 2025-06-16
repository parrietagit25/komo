<?php
class Stand {
    private $conn;
    private $table = "stands";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($nombre, $ubicacion) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nombre, ubicacion, stat) VALUES (:nombre, :ubicacion, 1)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':ubicacion', $ubicacion);
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
