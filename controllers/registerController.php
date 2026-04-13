<?php
require_once __DIR__ . "/../config/database.php";


$db = new Database();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $passwordRaw = trim($_POST["password"] ?? "");
    $confirmPassword = trim($_POST["confirmPassword"] ?? "");

    if (!$name || !$email || !$passwordRaw || !$confirmPassword) {
        $_SESSION["error"] = "Tous les champs sont obligatoires.";
        header("Location: ../Views/frontoffice/register.php");
        exit;
    }

    if ($passwordRaw !== $confirmPassword) {
        $_SESSION["error"] = "Les mots de passe ne correspondent pas.";
        header("Location: ../Views/frontoffice/register.php");
        exit;
    }

    // CHECK EMAIL
    $check = $conn->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $_SESSION["error"] = "Email déjà utilisé.";
        header("Location: ../Views/frontoffice/register.php");
        exit;
    }

    $password = password_hash($passwordRaw, PASSWORD_DEFAULT);
    $role = "user";

    $stmt = $conn->prepare(
        "INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)"
    );

    if ($stmt->execute([$name, $email, $password, $role])) {
        $_SESSION["success"] = "Compte créé avec succès !";
    } else {
        $_SESSION["error"] = "Erreur lors de la création.";
    }

    header("Location: ../Views/frontoffice/register.php");
    exit;
}