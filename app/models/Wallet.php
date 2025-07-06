<?php
class Wallet {
    private $conn;
    private $table = "cash_wallet";
    private $table2 = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function ver_todas_transacciones(){
        $stmt = $this->conn->prepare("select 
                                        cw.id,
                                        cw.monto,
                                        cw.fecha_log,
                                        u.nombre_completo, 
                                        u.id as id_user
                                        from cash_wallet cw inner join usuarios u on cw.id_user = u.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($data) {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (id_user, monto, stat) 
                                      VALUES (:id_user, :monto, :stat)");
        $stmt->execute([
            ':id_user' => $data['id_user'],
            ':monto' => $data['monto'],
            ':stat' => 1
        ]);
    }

    public function actualizar($data) {
        $stmt = $this->conn->prepare("UPDATE $this->table SET id_user = :id_user, monto = :monto WHERE id = :id");
        $stmt->execute([
            ':id_user' => $data['id_user'],
            ':monto' => $data['monto'], 
            ':id' => $data['id']
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

}
