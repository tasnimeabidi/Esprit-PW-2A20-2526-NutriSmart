<?php

require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $_SESSION["error"] = "Tous les champs sont obligatoires.";
        header("Location: ../Views/frontoffice/login.php");
        exit();
    }

    // PDO query
    $stmt = $conn->prepare("SELECT id_utilisateur, nom, mot_de_passe FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        if (password_verify($password, $user["mot_de_passe"])) {

            $_SESSION["user_id"] = $user["id_utilisateur"];
            $_SESSION["user_name"] = $user["nom"];

            header("Location: ../Views/frontoffice/blog.php");
            exit();

        } else {
            $_SESSION["error"] = "Mot de passe incorrect.";
        }

    } else {
        $_SESSION["error"] = "Email introuvable.";
    }

    header("Location: ../Views/frontoffice/login.php");
    exit();
}