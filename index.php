<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config/database.php";
require_once "Models/User.php";
require_once "Models/Publication.php";
require_once "controllers/AuthController.php";

$db = (new Database())->connect();

$action = $_GET['action'] ?? 'home';
switch ($action) {

    case "login":
        (new AuthController($db))->login();
        break;

    case "register":
        (new AuthController($db))->register();
        break;

    case "logout":
        session_unset();
        session_destroy();
        header("Location: index.php?action=login");
        exit;

    case "blog":
        require "controllers/PublicationController.php";
        break;

    case "create":
    case "delete":
    case "update":
        require "controllers/PublicationController.php";
        break;

    case "admin_dashboard":
        require "Views/backoffice/nutrismart-dashboard.php";
        break;

    default:
        require "Views/frontoffice/nutrismart-website.php";
        break;
}