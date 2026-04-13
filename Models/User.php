<?php

class User {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($nom, $email, $password) {

        $query = "INSERT INTO utilisateur (nom, email, mot_de_passe, role)
                  VALUES (:nom, :email, :password, 'user')";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);

        return $stmt->execute();
    }

    public function login($email) {

        $query = "SELECT * FROM utilisateur WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}