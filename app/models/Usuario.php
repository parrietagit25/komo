<?php
class Usuario {
    private $conn;
    private $table = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function validarUsuario($usuario, $password) {
        
        $query = "SELECT * FROM " . $this->table . " WHERE usuario = :usuario AND stat = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {

                return $row;
            }
        }

        return false;
    }

    public function registrar($data) {
        $query = "INSERT INTO usuarios (usuario, nombre_completo, email, tipo_usuario, password, stat) 
                VALUES (:usuario, :nombre_completo, :email, :tipo_usuario, :password, :stat)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':usuario' => $data['usuario'],
            ':nombre_completo' => $data['nombre_completo'],
            ':email' => $data['email'],
            ':tipo_usuario' => $data['tipo_usuario'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT), 
            ':stat'=>1
        ]);
        return true;
    }

    public function actualizar($data) {
        $query = "UPDATE usuarios SET usuario = :usuario, nombre_completo = :nombre_completo,
                email = :email, tipo_usuario = :tipo_usuario WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':usuario' => $data['usuario'],
            ':nombre_completo' => $data['nombre_completo'],
            ':email' => $data['email'],
            ':tipo_usuario' => $data['tipo_usuario'],
            ':id' => $data['id']
        ]);
        return true;
    }

    public function eliminar($id) {
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function todos_usuarios(){
        $query = "SELECT * FROM usuarios";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function todos_usuarios_stand(){
        $query = "SELECT * FROM usuarios where tipo_usuario = 2";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
