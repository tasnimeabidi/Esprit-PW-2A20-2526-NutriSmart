<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ProjetNutrismart/index.php?action=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard - NutriSmart</title>

    <link rel="stylesheet" href="/ProjetNutrismart/css/shared-styles.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f7f5;
        }

        .topbar {
            background: #2d6a2d;
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar .logout {
            background: #f2994a;
            padding: 8px 12px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
        }

        .container {
            padding: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .card h3 {
            margin: 0 0 10px;
        }

        .card p {
            margin: 0;
            color: #666;
        }
    </style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <div>
        Admin Dashboard — NutriSmart
    </div>

    <div>
        <?= htmlspecialchars($_SESSION['nom']) ?>
        <a class="logout" href="/ProjetNutrismart/index.php?action=logout">Logout</a>
    </div>
</div>

<!-- CONTENT -->
<div class="container">

    <h2>Bienvenue Admin</h2>
    <p>Gestion de la plateforme NutriSmart</p>

    <div class="grid">

        <div class="card">
            <h3>Utilisateurs</h3>
            <p>Gérer les comptes utilisateurs</p>
        </div>

        <div class="card">
    <h3>Blog</h3>
    <p>Créer et gérer les publications</p>
    <a href="/ProjetNutrismart/index.php?action=blog">Gérer le blog</a>
</div>
</a>
        </div>

        <div class="card">
            <h3>Recettes</h3>
            <p>Gestion des recettes</p>
        </div>

        <div class="card">
            <h3>Statistiques</h3>
            <p>Voir les performances du site</p>
        </div>

    </div>

</div>

</body>
</html>