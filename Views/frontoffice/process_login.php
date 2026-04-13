<?php
// ============================================
// CRUD - FRONT OFFICE : Lecture / Auth (READ)
// ============================================
// Ce fichier vérifie si l'utilisateur existe dans la base de données.
// Il reçoit l'email et le mot de passe de 'login.html'.
require_once __DIR__ . '/../../controllers/UserController.php';
$controller = new UserController();
$result = $controller->login();

if ($result && !$result['success']) {
    echo "<script>
        alert('Erreur : " . addslashes($result['message']) . "');
        window.history.back();
    </script>";
}
?>
