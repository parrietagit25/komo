<?php
class Evento {
    private $conn;
    private $table = "eventos";
    private $table2 = "asig_stand_even";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodosEventos() {
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

    public function asignar_stand($data){

        $stmt = $this->conn->prepare("INSERT INTO $this->table2 (id_even, id_stand, comentario) 
                                      VALUES (:id_even, :id_stand, :comentario)");
        $stmt->execute([
            ':id_even' => $data['id_even'],
            ':id_stand' => $data['id_stand'], 
            ':comentario' => $data['comentario']
        ]);

    }

    public function ver_asignados($id){
        $stmt = $this->conn->prepare("SELECT
                                        e.nombre as nombre_evento, 
                                        s.nombre as nombre_stand, 
                                        ase.fecha_log, 
                                        ase.stat
                                        FROM asig_stand_even ase inner join eventos e on ase.id_even = e.id 
                                                                inner join stands s on ase.id_stand = s.id
                                        WHERE 
                                        ase.id_even = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}