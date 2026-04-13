<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php?action=login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion Blog - Admin</title>

  <link rel="stylesheet" href="/ProjetNutrismart/css/shared-styles.css">
  <script src="/ProjetNutrismart/public/js/publication-validation.js"></script>

  <style>
    body {
      margin: 0;
      font-family: Arial;
      background: #f5f5f5;
    }

    .container {
      padding: 30px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    .btn {
      padding: 6px 10px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
    }

    .delete {
      background: #e74c3c;
      color: white;
    }

    .edit {
      background: #3498db;
      color: white;
    }

    .stats {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }

    .stat-box {
      background: white;
      padding: 15px;
      border-radius: 10px;
      flex: 1;
    }
  </style>
</head>

<body>

<div class="container">

<?php
$totalPosts = count($posts);
?>

<div class="header">
  <h1>Gestion des posts</h1>
  <a href="/ProjetNutrismart/index.php?action=admin_dashboard">← Dashboard</a>
</div>

<div class="stats">
  <div class="stat-box">
    <h3><?= $totalPosts ?></h3>
    <p>Total posts</p>
  </div>
</div>

<div class="card">

<table>
  <thead>
    <tr>
      <th>Titre</th>
      <th>Auteur</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($posts as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['titre']) ?></td>
        <td><?= htmlspecialchars($p['nom']) ?></td>
        <td><?= $p['date_publication'] ?></td>
        <td>
          <a class="btn delete"
             href="/ProjetNutrismart/index.php?action=delete&id=<?= $p['id_publication'] ?>">
             Delete
          </a>

          <a class="btn edit"
             href="#">
             Edit
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>

</table>

</div>

</div>

</body>
</html>