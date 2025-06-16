<?php
class Evento {
    private $conn;
    private $table = "eventos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($data) {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (nombre, fecha, ubicacion, descripcion, estado) 
                                      VALUES (:nombre, :fecha, :ubicacion, :descripcion, :estado)");
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':fecha' => $data['fecha'],
            ':ubicacion' => $data['ubicacion'],
            ':descripcion' => $data['descripcion'],
            ':estado' => $data['estado']
        ]);
    }

    public function actualizar($data) {
        $stmt = $this->conn->prepare("UPDATE $this->table SET nombre = :nombre, fecha = :fecha, ubicacion = :ubicacion,
                                      descripcion = :descripcion, estado = :estado WHERE id = :id");
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':fecha' => $data['fecha'],
            ':ubicacion' => $data['ubicacion'],
            ':descripcion' => $data['descripcion'],
            ':estado' => $data['estado'],
            ':id' => $data['id']
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
