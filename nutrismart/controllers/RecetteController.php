<?php
include_once __DIR__ . '/../Models/config.php';
include_once __DIR__ . '/../Models/Recette.php';

class RecetteController {
    private $db;
    private $recette;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->recette = new Recette($this->db);
    }

    public function listRecettes() {
        $stmt = $this->recette->readAll();
        $recettes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recettes[] = $row;
        }
        return $recettes;
    }

    public function getRecette($id) {
        $this->recette->id = $id;
        if ($this->recette->readOne()) {
            return [
                "id" => $this->recette->id,
                "nom_recette" => $this->recette->nom_recette,
                "instructions" => $this->recette->instructions,
                "calories_totales" => $this->recette->calories_totales
            ];
        }
        return null;
    }

    public function createRecette($data) {
        $this->recette->nom_recette = $data['nom_recette'] ?? '';
        $this->recette->instructions = $data['instructions'] ?? '';
        $this->recette->calories_totales = $data['calories_totales'] ?? 0;

        if ($this->recette->create()) {
            return ["status" => "success", "message" => "Recette créée avec succès"];
        }
        return ["status" => "error", "message" => "Échec de la création de la recette"];
    }

    public function updateRecette($data) {
        $this->recette->id = $data['id'];
        $this->recette->nom_recette = $data['nom_recette'] ?? '';
        $this->recette->instructions = $data['instructions'] ?? '';
        $this->recette->calories_totales = $data['calories_totales'] ?? 0;

        if ($this->recette->update()) {
            return ["status" => "success", "message" => "Recette mise à jour avec succès"];
        }
        return ["status" => "error", "message" => "Échec de la mise à jour de la recette"];
    }

    public function deleteRecette($id) {
        $this->recette->id = $id;
        if ($this->recette->delete()) {
            return ["status" => "success", "message" => "Recette supprimée avec succès"];
        }
        return ["status" => "error", "message" => "Échec de la suppression de la recette"];
    }
}

// Handle AJAX/API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $controller = new RecetteController();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        echo json_encode($controller->createRecette($_POST));
        exit;
    } elseif ($action === 'update') {
        echo json_encode($controller->updateRecette($_POST));
        exit;
    } elseif ($action === 'delete') {
        echo json_encode($controller->deleteRecette($_POST['id']));
        exit;
    }
}
?>
