/* Création de la base de données 'gvd' */
DROP DATABASE IF EXISTS gvd;
CREATE DATABASE gvd DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gvd;

/* Prise en compte des accents dans les requêtes d'insertions de données */
SET NAMES utf8mb4;

/* Table carburant + insertions */
DROP TABLE IF EXISTS `carburant`;
CREATE TABLE IF NOT EXISTS `carburant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `carburant` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `carburant` (`id`, `carburant`) VALUES
(1, 'Gasoil'),
(2, 'Essence'),
(3, 'Hybride'),
(4, 'Electrique'),
(5, 'Éthanol');

/* Table client + insertions */
DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `suite_adresse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_postal` int NOT NULL,
  `ville` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `client` (`id`, `nom`, `prenom`, `tel`, `email`, `adresse`, `suite_adresse`, `code_postal`, `ville`) VALUES
(1, 'Deschênes', 'Pierrette', '0433453716', 'pierrettedeschenes@test.com', '68 rue des Soeurs', NULL, 13600, 'LA CIOTAT'),
(2, 'Nutman', 'Lief', '0155580653', 'liefnutman@test.com', '4 Lotheville Lane', NULL, 92600, 'ASNIÈRES-SUR-SEINE'),
(3, 'Richer', 'Roslyn', '0156816643', 'roslynricher@test.com', '79 rue de Lille', NULL, 75017, 'PARIS'),
(4, 'Grivois', 'Thérèse', '0274989065', 'theresegrivois@test.com', '28 Chemin Des Bateliers', NULL, 49100, 'ANGERS'),
(5, 'Riquier', 'Avril', '0126461863', 'avrilriquier@test.com', '97 Square de la Couronne', NULL, 91120, 'PALAISEAU'),
(6, 'Richer', 'Roslyn', '0856570754', 'roslynRicher@test.com', '79 rue de Lille', NULL, 75017, 'PARIS'),
(7, 'Proulx', 'Richard', '0591832547', 'richardproulx@test.com', '9 Avenue des Tuileries', NULL, 23000, 'GUÉRET'),
(8, 'Rochefort', 'Florus', '0235039833', 'florusrochefort@test.com', '79 rue de Lille', NULL, 75017, 'PARIS'),
(9, 'Phaneuf', 'Victoire', '0404209618', 'vctoirepaneuf@test.com', '26 rue Beauvau', NULL, 13003, 'MARSEILLE'),
(10, 'Tétrault', 'Étienne', '0441213577', 'etiennetetrault@test.com', '7 cours Franklin Roosevelt', NULL, 13007, 'MARSEILLE'),
(11, 'Tougas', 'Thibaut', '0163501697', 'thibauttougas@test.com', '79 rue de Lille', NULL, 93270, 'SEVRAN');


/* Table tva + insertions */
DROP TABLE IF EXISTS `tva`;
CREATE TABLE IF NOT EXISTS `tva` (
  `id` int NOT NULL AUTO_INCREMENT,
  `taux` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tva` (`id`, `taux`) VALUES
(1, 20);


/* Table type_etat + insertions */
DROP TABLE IF EXISTS `type_etat`;
CREATE TABLE IF NOT EXISTS `type_etat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `type_etat` (`id`, `type`) VALUES
(1, 'intervention'),
(2, 'vehicule');


/* Table marque + insertions */
DROP TABLE IF EXISTS `marque`;
CREATE TABLE IF NOT EXISTS `marque` (
  `id` int NOT NULL AUTO_INCREMENT,
  `marque` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `marque` (`id`, `marque`) VALUES
(1, 'CITROEN'),
(2, 'DACIA'),
(3, 'FIAT'),
(4, 'FORD'),
(5, 'KIA'),
(6, 'NISSAN'),
(7, 'OPEL'),
(8, 'PEUGEOT'),
(9, 'RENAULT'),
(10, 'VOLKSWAGEN'),
(11, 'TESLA');


/* Table modele + insertions */
DROP TABLE IF EXISTS `modele`;
CREATE TABLE IF NOT EXISTS `modele` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_marque_id` int NOT NULL,
  `modele` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_10028558297E6E22` (`fk_marque_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `modele` (`id`, `fk_marque_id`, `modele`) VALUES
(1, 1, 'C2'),
(3, 1, 'C4'),
(4, 1, 'DS3'),
(5, 1, 'DS4'),
(6, 1, 'DS5'),
(7, 1, 'SAXO'),
(8, 1, 'XANTIA'),
(9, 1, 'XSARA'),
(10, 2, 'DUSTER'),
(11, 2, 'LOGAN'),
(12, 2, 'SANDERO'),
(13, 3, '500'),
(14, 3, 'MULTIPLA'),
(15, 4, 'FIESTA'),
(16, 4, 'FOCUS'),
(17, 5, 'CEED'),
(18, 5, 'SPORTAGE'),
(19, 6, 'JUKE'),
(20, 6, 'QASHQAI'),
(21, 6, 'X-TRAIL'),
(22, 7, 'ASTRA'),
(23, 7, 'INSIGNIA'),
(24, 7, 'MERIVA'),
(25, 7, 'MOKKA'),
(26, 7, 'ZAFIRA'),
(27, 8, '206'),
(28, 8, '207'),
(29, 8, '208'),
(30, 8, '307'),
(31, 8, '308'),
(32, 8, '406'),
(33, 8, '407'),
(34, 8, '5008'),
(35, 9, 'CAPTUR'),
(36, 9, 'CLIO'),
(37, 9, 'ESPACE'),
(38, 9, 'LAGUNA'),
(39, 9, 'MEGANE'),
(40, 9, 'SCENIC'),
(41, 9, 'TWINGO'),
(42, 10, 'COCCINELLE'),
(43, 10, 'GOLF'),
(44, 10, 'ID.4'),
(45, 10, 'PASSAT'),
(46, 10, 'POLO'),
(47, 10, 'TIGUAN'),
(48, 10, 'TOUAREG'),
(49, 11, 'MODEL 3'),
(50, 1, 'C3');


/* Table moyen_paiement + insertions */
DROP TABLE IF EXISTS `moyen_paiement`;
CREATE TABLE IF NOT EXISTS `moyen_paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `moyen_paiement` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `moyen_paiement` (`id`, `moyen_paiement`) VALUES
(1, 'Carte bancaire'),
(2, 'Virement'),
(3, 'Chèque');


/**
  Table messenger_messages
  Ajouté par Symfony
  */
DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/**
  Table doctrine_migration_versions + insertions
  Ajouté par Symfony
  */
DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20231217222558', '2023-12-17 22:27:31', 480),
('DoctrineMigrations\\Version20231218172708', '2023-12-18 17:27:31', 1611),
('DoctrineMigrations\\Version20231218181155', '2023-12-18 18:12:20', 627),
('DoctrineMigrations\\Version20231222212857', '2023-12-22 21:29:09', 674);


/* Table etat + insertions */
DROP TABLE IF EXISTS `etat`;
CREATE TABLE IF NOT EXISTS `etat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_type_etat_id` int NOT NULL,
  `etat` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_55CAF7624A2EB13` (`fk_type_etat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `etat` (`id`, `fk_type_etat_id`, `etat`) VALUES
(1, 1, 'En attente'),
(2, 1, 'Terminé'),
(3, 1, 'Facturé'),
(4, 2, 'Fonctionnel'),
(5, 2, 'Hors service');


/* Table facture + insertions */
DROP TABLE IF EXISTS `facture`;
CREATE TABLE IF NOT EXISTS `facture` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_taux_id` int NOT NULL,
  `fk_moyen_paiement_id` int DEFAULT NULL,
  `date_facture` date NOT NULL,
  `date_paiement` date DEFAULT NULL,
  `montant_ht` double NOT NULL,
  `montant_tva` double NOT NULL,
  `montant_ttc` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FE866410B075317B` (`fk_taux_id`),
  KEY `IDX_FE8664105249AB64` (`fk_moyen_paiement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `facture` (`id`, `fk_taux_id`, `fk_moyen_paiement_id`, `date_facture`, `date_paiement`, `montant_ht`, `montant_tva`, `montant_ttc`) VALUES
(1, 1, 1, '2024-01-26', '2024-01-26', 80, 16, 96),
(2, 1, 3, '2024-01-26', '2024-01-26', 130, 26, 156),
(3, 1, 3, '2024-01-26', '2024-01-26', 485, 97, 582),
(4, 1, 2, '2024-01-26', '2024-01-26', 150, 30, 180);


/* Table vehicule + insertions */
DROP TABLE IF EXISTS `vehicule`;
CREATE TABLE IF NOT EXISTS `vehicule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_client_id` int NOT NULL,
  `fk_modele_id` int NOT NULL,
  `fk_carburant_id` int NOT NULL,
  `fk_etat_id` int NOT NULL,
  `immatriculation` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kilometrage` bigint NOT NULL,
  `annee` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_292FFF1D78B2BEB1` (`fk_client_id`),
  KEY `IDX_292FFF1DCD4D609A` (`fk_modele_id`),
  KEY `IDX_292FFF1D1307AF3D` (`fk_carburant_id`),
  KEY `IDX_292FFF1DFD71BBD3` (`fk_etat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `vehicule` (`id`, `fk_client_id`, `fk_modele_id`, `fk_carburant_id`, `fk_etat_id`, `immatriculation`, `kilometrage`, `annee`) VALUES
(1, 1, 1, 2, 4, 'TW-012-ET', 70000, 2014),
(2, 2, 1, 1, 4, 'QY-228-JO', 200000, 2017),
(3, 3, 3, 2, 5, 'WK-883-XB', 90000, 2013),
(4, 4, 4, 2, 4, 'ZS-933-GF', 150000, 2015),
(5, 4, 8, 2, 4, 'MI-712-PA', 278564, 2010),
(6, 7, 4, 1, 4, 'PS-671-LY', 312096, 2012),
(7, 9, 9, 2, 4, 'ZZ-321-AA', 264567, 2013);


/* Table intervention + insertions */
DROP TABLE IF EXISTS `intervention`;
CREATE TABLE IF NOT EXISTS `intervention` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_vehicule_id` int NOT NULL,
  `fk_facture_id` int DEFAULT NULL,
  `fk_etat_id` int NOT NULL,
  `date_creation` date NOT NULL,
  `date_intervention` date NOT NULL,
  `duree` smallint NOT NULL,
  `detail` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant_ht` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D11814AB23BC9925` (`fk_vehicule_id`),
  KEY `IDX_D11814AB8F43249B` (`fk_facture_id`),
  KEY `IDX_D11814ABFD71BBD3` (`fk_etat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `intervention` (`id`, `fk_vehicule_id`, `fk_facture_id`, `fk_etat_id`, `date_creation`, `date_intervention`, `duree`, `detail`, `montant_ht`) VALUES
(1, 1, 1, 3, '2024-01-21', '2024-01-22', 1, 'Révision', 80),
(2, 2, 2, 3, '2024-01-22', '2024-01-23', 2, 'Changement pneus\r\nParallélisme\r\nChangement capot moteur', 130),
(3, 3, 3, 3, '2024-01-23', '2024-01-24', 1, 'Contrôle technique', 120),
(4, 4, 4, 3, '2024-01-24', '2024-01-25', 2, 'Ligne echappement', 150),
(5, 3, 3, 3, '2024-01-25', '2024-01-26', 2, 'changement toit et parebrise', 250),
(6, 3, 3, 3, '2024-01-25', '2024-01-26', 1, 'pneus\r\nvidange', 115),
(7, 7, NULL, 2, '2024-01-25', '2024-01-26', 1, 'Changement rétro droit', 65);


/* Table utilisateur + insertions */
DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1D1C63B3E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `utilisateur` (`id`, `email`, `roles`, `password`, `nom`, `prenom`) VALUES
(1, 'utilisateur@gvd.test', '[\"ROLE_USER\"]', '$2y$13$XLR8GIvul6YwfmAmIJeKU.Stzvz2gBL9lBPEYByiAdi83n/cTx4Ry', 'DUPONT', 'Thomas'),
(2, 'administrateur@gvd.test', '[\"ROLE_ADMIN\"]', '$2y$13$UkTKP4gX0DtvrQgDxozgqeX02VaAoHXilctKTC0NVecqdsZpRGGWK', 'DUPOND', 'Pascal');


/* Contraintes pour les tables */
ALTER TABLE `etat`
  ADD CONSTRAINT `FK_55CAF7624A2EB13` FOREIGN KEY (`fk_type_etat_id`) REFERENCES `type_etat` (`id`);

ALTER TABLE `facture`
  ADD CONSTRAINT `FK_FE8664105249AB64` FOREIGN KEY (`fk_moyen_paiement_id`) REFERENCES `moyen_paiement` (`id`),
  ADD CONSTRAINT `FK_FE866410B075317B` FOREIGN KEY (`fk_taux_id`) REFERENCES `tva` (`id`);

ALTER TABLE `intervention`
  ADD CONSTRAINT `FK_D11814AB23BC9925` FOREIGN KEY (`fk_vehicule_id`) REFERENCES `vehicule` (`id`),
  ADD CONSTRAINT `FK_D11814AB8F43249B` FOREIGN KEY (`fk_facture_id`) REFERENCES `facture` (`id`),
  ADD CONSTRAINT `FK_D11814ABFD71BBD3` FOREIGN KEY (`fk_etat_id`) REFERENCES `etat` (`id`);

ALTER TABLE `modele`
  ADD CONSTRAINT `FK_10028558297E6E22` FOREIGN KEY (`fk_marque_id`) REFERENCES `marque` (`id`);

ALTER TABLE `vehicule`
  ADD CONSTRAINT `FK_292FFF1D1307AF3D` FOREIGN KEY (`fk_carburant_id`) REFERENCES `carburant` (`id`),
  ADD CONSTRAINT `FK_292FFF1D78B2BEB1` FOREIGN KEY (`fk_client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_292FFF1DCD4D609A` FOREIGN KEY (`fk_modele_id`) REFERENCES `modele` (`id`),
  ADD CONSTRAINT `FK_292FFF1DFD71BBD3` FOREIGN KEY (`fk_etat_id`) REFERENCES `etat` (`id`);