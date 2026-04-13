-- Create database
CREATE DATABASE IF NOT EXISTS `nutrismart` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `nutrismart`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Table: utilisateur
CREATE TABLE `utilisateur` (
  `id_utilisateur` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'utilisateur',
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `uk_utilisateur_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: aliment
CREATE TABLE `aliment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `categorie` varchar(32) NOT NULL DEFAULT 'autre',
  `calories_100g` decimal(8,2) DEFAULT NULL,
  `proteines_100g` decimal(8,2) DEFAULT NULL,
  `glucides_100g` decimal(8,2) DEFAULT NULL,
  `lipides_100g` decimal(8,2) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: plan_repas
CREATE TABLE `plan_repas` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(10) UNSIGNED NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `objectif` varchar(255) NOT NULL,
  `statut` varchar(64) NOT NULL DEFAULT 'brouillon',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur`(`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: recette
CREATE TABLE `recette` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `instructions` text,
  `calories_totales` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: programme_sportif
CREATE TABLE `programme_sportif` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_plan` int(10) UNSIGNED NOT NULL,
  `type_sport` varchar(128) NOT NULL,
  `niveau` varchar(64) NOT NULL,
  `intensite` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_plan`) REFERENCES `plan_repas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: repas
CREATE TABLE `repas` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_plan` int(10) UNSIGNED NOT NULL,
  `id_recette` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `calories` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_plan`) REFERENCES `plan_repas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_recette`) REFERENCES `recette`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: journal_nutrition
CREATE TABLE `journal_nutrition` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(10) UNSIGNED NOT NULL,
  `id_aliment` int(10) UNSIGNED NOT NULL,
  `date_entree` date NOT NULL,
  `calories` int(10) UNSIGNED DEFAULT NULL,
  `quantite` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur`(`id_utilisateur`) ON DELETE CASCADE,
  FOREIGN KEY (`id_aliment`) REFERENCES `aliment`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: journal_sport
CREATE TABLE `journal_sport` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(10) UNSIGNED NOT NULL,
  `date_seance` date NOT NULL,
  `type_sport` varchar(128) NOT NULL,
  `duree_min` smallint UNSIGNED NOT NULL,
  `calories_depensees` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur`(`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;