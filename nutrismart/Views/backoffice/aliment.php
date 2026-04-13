<?php
include_once '../../controllers/AlimentController.php';
include_once '../../controllers/RecetteController.php';
$controller = new AlimentController();
$recetteController = new RecetteController();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_aliment') {
            $controller->createAliment($_POST);
        } elseif ($_POST['action'] === 'delete_aliment') {
            $controller->deleteAliment($_POST['id']);
        } elseif ($_POST['action'] === 'update_aliment') {
            $controller->updateAliment($_POST);
        }
    }
    header("Location: aliment.php");
    exit();
}

$aliments = $controller->listAliments();
$recettes = $recetteController->listRecettes();


// Si ?edit=ID dans l'URL → charger l'aliment à modifier
$editAliment = null;
if (isset($_GET['edit'])) {
    $editAliment = $controller->getAliment((int)$_GET['edit']);
}

// Charger la vue (le HTML séparé)
include 'aliment.html';
?>
