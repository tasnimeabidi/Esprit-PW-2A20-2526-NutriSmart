<?php
class Recette {
    private $conn;
    private $table_name = "recette";

    public $id;
    public $nom_recette;
    public $instructions;
    public $calories_totales;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nom_recette, instructions, calories_totales) 
                  VALUES (:nom_recette, :instructions, :calories_totales)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nom_recette', $this->nom_recette);
        $stmt->bindParam(':instructions', $this->instructions);
        $stmt->bindParam(':calories_totales', $this->calories_totales);

        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->nom_recette = $row['nom_recette'];
            $this->instructions = $row['instructions'];
            $this->calories_totales = $row['calories_totales'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nom_recette = :nom_recette, 
                      instructions = :instructions, 
                      calories_totales = :calories_totales 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nom_recette', $this->nom_recette);
        $stmt->bindParam(':instructions', $this->instructions);
        $stmt->bindParam(':calories_totales', $this->calories_totales);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
