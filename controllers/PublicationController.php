<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Models/Publication.php';

$db = (new Database())->connect();
$postModel = new Publication($db);

$action = $_GET['action'] ?? 'blog';

/* =========================
   PUBLIC BLOG
========================= */
if ($action === "blog") {

    $posts = $postModel->getAll();

    if (!is_array($posts)) {
        $posts = [];
    }

    require __DIR__ . '/../Views/frontoffice/blog.php';
    exit;
}

/* =========================
   CREATE POST
========================= */
if ($action === "create") {

    if (!isset($_SESSION['id_utilisateur'])) {
        header("Location: index.php?action=login");
        exit;
    }

    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');

    if ($titre === '' || $contenu === '') {
        header("Location: index.php?action=blog");
        exit;
    }

    $image = null;

    if (!empty($_FILES['image']['name'])) {

        $uploadDir = __DIR__ . "/../public/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    }

    $postModel->create($_SESSION['id_utilisateur'], $titre, $contenu, $image);

    header("Location: index.php?action=blog");
    exit;
}

/* =========================
   DELETE POST
========================= */
if ($action === "delete") {

    if (!isset($_SESSION['id_utilisateur'])) {
        header("Location: index.php?action=login");
        exit;
    }

    $id = $_GET['id'] ?? null;

    if ($id) {
        $post = $postModel->getById($id);

        if ($post && $post['id_utilisateur'] == $_SESSION['id_utilisateur']) {
            $postModel->delete($id);
        }
    }

    header("Location: index.php?action=blog");
    exit;
}

/* =========================
   UPDATE POST
========================= */
if ($action === "update") {

    if (!isset($_SESSION['id_utilisateur'])) {
        header("Location: index.php?action=login");
        exit;
    }

    $id = $_POST['id'] ?? null;

    if (!$id) {
        header("Location: index.php?action=blog");
        exit;
    }

    $post = $postModel->getById($id);

    if (!$post || $post['id_utilisateur'] != $_SESSION['id_utilisateur']) {
        header("Location: index.php?action=blog");
        exit;
    }

    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');

    $image = $post['image'];

    if (!empty($_FILES['image']['name'])) {

        $uploadDir = __DIR__ . "/../public/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    }

    $postModel->update($id, $titre, $contenu, $image);

    header("Location: index.php?action=blog");
    exit;
}