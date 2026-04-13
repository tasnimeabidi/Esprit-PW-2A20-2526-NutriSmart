<?php
// ============================================
// CRUD - FRONT OFFICE : Mettre en place (CREATE)
// ============================================
// Ce fichier est appelé lorsque le formulaire 'register.html' est soumis.
// Il utilise le contrôleur pour enregistrer le nouvel utilisateur dans la base.
require_once __DIR__ . '/../../controllers/UserController.php';
$controller = new UserController();
$result = $controller->register();

if ($result && !$result['success']) {
    echo "<script>
        alert('Erreur : " . addslashes($result['message']) . "');
        window.history.back();
    </script>";
}
?>
