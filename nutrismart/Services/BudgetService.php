<?php
require_once __DIR__ . '/../config/db_connect.php';

class BudgetService {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function setBudget($userId, $montant) {
        $sql = "INSERT INTO budget (id_utilisateur, montant) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE montant = VALUES(montant)";
        return $this->db->prepare($sql)->execute([$userId, $montant]);
    }

    public function getBudgetInfo($userId) {
        $sql = "SELECT b.*, 
                (SELECT SUM(prix_total) FROM user_achat WHERE id_utilisateur = b.id_utilisateur) as total_depense 
                FROM budget b WHERE b.id_utilisateur = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}