-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 10 jan. 2026 à 09:01
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `innovshop`
--

-- --------------------------------------------------------

--
-- Structure de la table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_default` tinyint(1) DEFAULT NULL,
  `billing_default` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D4E6F81A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `address`
--

INSERT INTO `address` (`id`, `user_id`, `first_name`, `name`, `phone`, `address`, `postal_code`, `city`, `type`, `delivery_default`, `billing_default`) VALUES
(1, 3, 'Simon', 'Thonneau', NULL, '2 route du test', '12345', 'testville', 'livraison', 1, NULL),
(2, 3, 'Simon', 'Thonneau', NULL, '2 route du test', '12345', 'testville', 'facturation', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Ordinateurs', ''),
(2, 'Tablettes', ''),
(3, 'Composants', ''),
(4, 'Accessoires', ''),
(5, 'Jeux vidéo', '');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251222172626', '2025-12-22 17:26:45', 696),
('DoctrineMigrations\\Version20251223123802', '2025-12-23 12:38:27', 175),
('DoctrineMigrations\\Version20251225131701', '2025-12-25 13:17:14', 79),
('DoctrineMigrations\\Version20251229163906', '2025-12-29 16:39:47', 52),
('DoctrineMigrations\\Version20251229165429', '2025-12-29 16:54:36', 42),
('DoctrineMigrations\\Version20260101145852', '2026-01-01 14:59:07', 132),
('DoctrineMigrations\\Version20260103133050', '2026-01-03 13:31:01', 77),
('DoctrineMigrations\\Version20260104142053', '2026-01-04 14:21:01', 80),
('DoctrineMigrations\\Version20260109140349', '2026-01-09 14:04:08', 106),
('DoctrineMigrations\\Version20260109150350', '2026-01-09 15:06:40', 425);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messenger_messages`
--

INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(1, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:39:\\\"Symfony\\\\Bridge\\\\Twig\\\\Mime\\\\TemplatedEmail\\\":5:{i:0;s:41:\\\"registration/confirmation_email.html.twig\\\";i:1;N;i:2;a:3:{s:9:\\\"signedUrl\\\";s:166:\\\"http://127.0.0.1:8000/verify/email?expires=1766675242&signature=TAYYoCTAoJBrjGKXXambUDsh779_8Xmbt1_uFjuScw0&token=QNSdF%2F%2Bb5iSpGpKDLB5jxEFU%2B07oB2Pje91yMfWBIOU%3D\\\";s:19:\\\"expiresAtMessageKey\\\";s:26:\\\"%count% hour|%count% hours\\\";s:20:\\\"expiresAtMessageData\\\";a:1:{s:7:\\\"%count%\\\";i:1;}}i:3;a:6:{i:0;N;i:1;N;i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"simontestdev86@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:7:\\\"MailBot\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:19:\\\"thonneau@hotmail.fr\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:25:\\\"Please Confirm your Email\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}i:4;N;}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2025-12-25 14:07:22', '2025-12-25 14:07:22', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

DROP TABLE IF EXISTS `options`;
CREATE TABLE IF NOT EXISTS `options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `options`
--

INSERT INTO `options` (`id`, `name`) VALUES
(1, '16Go RAM 512Go SSD'),
(2, '16Go RAM 1To SSD'),
(3, '32Go RAM 512Go SSD'),
(4, '32Go RAM 1To SSD');

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` double NOT NULL,
  `date` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `delivery_address_id` int NOT NULL,
  `billing_address_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E52FFDEEA76ED395` (`user_id`),
  KEY `IDX_E52FFDEEEBF23851` (`delivery_address_id`),
  KEY `IDX_E52FFDEE79D0C0E4` (`billing_address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `numero`, `total`, `date`, `status`, `user_id`, `delivery_address_id`, `billing_address_id`) VALUES
(6, NULL, 828.29, NULL, 'panier', 3, 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `order_lines`
--

DROP TABLE IF EXISTS `order_lines`;
CREATE TABLE IF NOT EXISTS `order_lines` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `orders_id` int NOT NULL,
  `subtotal` double NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CC9FF86B4584665A` (`product_id`),
  KEY `IDX_CC9FF86BCFFE9AD6` (`orders_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_lines`
--

INSERT INTO `order_lines` (`id`, `product_id`, `orders_id`, `subtotal`, `quantity`, `unit_price`) VALUES
(21, 7, 6, 768.5, 1, 768.5),
(22, 10, 6, 59.79, 1, 59.79);

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double NOT NULL,
  `stock` int NOT NULL,
  `une` tinyint(1) DEFAULT NULL,
  `date_add_une` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `image`, `price`, `stock`, `une`, `date_add_une`, `slug`) VALUES
(1, 'PC Gamer ASUS', '<div>Ce PC sera votre compagnon de jeu idéal. Son processeur ultra puissant, ses <strong>32Go de RAM</strong> et sa <strong>carte graphique RTX 5070</strong> font de cette machine un allier parfait aussi bien pour le montage vidéo, le multitâche et vos séances de gaming.</div>', 'anthony-roberts-5WJhuXkqCkc-unsplash.jpg', 1490.9, 6, 0, NULL, ''),
(2, 'PC Portable MacBook Air', '<div>Le nouveau<strong> MacBook Air</strong>, plus fin et plus léger, réunit un superbe <strong>écran Retina </strong>avec technologie True Tone, Touch ID, un clavier de dernière génération</div>', 'howard-bouchevereau-RSCirJ70NDM-unsplash.jpg', 1299.9, 2, 0, NULL, ''),
(3, 'PC Portable Apple', '<div>Superbe pc portable ! Idéal pour transporter votre travail dans tous vos déplacements.</div>', 'bosh-ar-1sSfrozgiFk-unsplash.jpg', 958, 3, 1, '2026-01-01 13:35:48', ''),
(4, 'PC Gaming', '<div>Idéal pour faire tourner tous vos jeux, même les plus gourmands...</div>', 'andre-tan-8yesL5ZPjIU-unsplash.jpg', 1534.99, 25, 0, NULL, ''),
(5, 'Tablette 128Go', '<div>Superbe tablette, pratique à transporter !</div>', 'henry-ascroft-7OFnb7NOvjw-unsplash.jpg', 249.8, 4, 0, NULL, ''),
(6, 'Tablette + stylet', '<div>Tablette conçue pour le graphisme.</div>', 'sanjeev-mohindra-aeSSYYWY27w-unsplash.jpg', 349.9, 19, 0, NULL, ''),
(7, 'PC portable hybride 2en1', '<div>PC portable convertible en tablette grâce à son clavier amovible</div>', 'serwin365-0cG_yQAdYIM-unsplash.jpg', 768.5, 8, 1, '2026-01-01 13:35:37', ''),
(8, 'Xbox', NULL, 'billy-freeman-DPOdCl4bGJU-unsplash.jpg', 499, 4, 0, NULL, ''),
(9, 'Playstation 1', '<div>Playsation premiere génération. Elle est très recherchée par le collectionneur et les passionés de rétro-gaming</div>', 'jonathan-cooper-dX_Y7f9T2G4-unsplash.jpg', 155.4, 2, 0, NULL, ''),
(10, 'Console portable Dynamo', '<div>Cette petite console portable est idéale pour s\'amuser en tous lieux. Facile à transporter, elle vous accompagnera dans tous vos déplacements. De plus, elle possède un système de dynamo (d\'où son nom) ce qui permet de la recharger facilement en quelques tours de molette.</div>', 'josh-withers-xU1Ypuem6S4-unsplash.jpg', 59.79, 21, 1, '2026-01-01 13:35:18', ''),
(11, 'Playstation 5', '<div>Playstation 5, dernier modèle de la marque. Cette console n\'est plus à présenter tant sa réputation est mondiale. Elle est <strong>livrée avec une manette</strong>.</div>', 'martin-katler-caNzzoxls8Q-unsplash.jpg', 690.5, 14, 0, NULL, ''),
(12, 'Gameboy 1ère génération', '<div>Gameboy 1ère génération. Parfait pour les amateurs de rétro-gaming qui cherche à renouer avec leurs souvenirs d\'enfant.</div>', 'nik-lUbIun4IL38-unsplash.jpg', 45.49, 9, 0, NULL, ''),
(13, 'RAM XPG 32Go (4x8Go) DDR5', '<div>4 barrettes de RAM de 8Go chacune, soit <strong>32Go DDR5.&nbsp;</strong>Parfait pour booster votre PC, le multitâche s\'effectuera sans latence et vos jeux seront ultra fluides.</div>', 'andrey-matveev-4n8JEK-rYmc-unsplash.jpg', 89.99, 30, 0, NULL, ''),
(14, 'Ventilateurs de PC', '<div>Ces 3 ventilateurs permettront à votre ordinateurs de garder une température optimale pour son fonctionnement.</div>', 'andrey-matveev-OB90vboC0N4-unsplash.jpg', 25.79, 18, 0, NULL, ''),
(15, 'Gigabyte RTX 5060', '<div>Jouez à tous vos jeux de manière optimale grâce à cette carte graphique RTX 5060 de la marque Gigabyte.&nbsp;</div>', 'andrey-matveev-Oq8kbZxP0Pw-unsplash.jpg', 459.9, 14, 0, NULL, ''),
(16, 'AMD Ryzen 5 2600', '<div>Cœur du PC, ce processeur ravira tout le monde. Que ce soit pour du gaming ou de la bureautique, ce composant essentiel vous permettra de réaliser toutes vos taches sans la moindre difficulté.</div>', 'remy-FeEpYGZX8Lc-unsplash.jpg', 349.9, 4, 0, NULL, ''),
(17, 'Antenne wi-fi usb', '<div>Vous ne pouvez pas relier votre ordinateur à votre box internet ? Pas de problème, nous avons le composant idéal pour vous ! Cette antenne wi-fi usb vous permettra de recevoir le wi-fi de votre box simplement en la connectant au port USB de votre ordinateur. De plus, elle ne nécessite aucune installation puisqu\'elle détecte automatiquement et rapidement tous les signaux wi-fi à proximité.</div>', NULL, 19.9, 13, 0, NULL, '');

-- --------------------------------------------------------

--
-- Structure de la table `products_categories`
--

DROP TABLE IF EXISTS `products_categories`;
CREATE TABLE IF NOT EXISTS `products_categories` (
  `products_id` int NOT NULL,
  `categories_id` int NOT NULL,
  PRIMARY KEY (`products_id`,`categories_id`),
  KEY `IDX_E8ACBE766C8A81A9` (`products_id`),
  KEY `IDX_E8ACBE76A21214B7` (`categories_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `products_categories`
--

INSERT INTO `products_categories` (`products_id`, `categories_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 2),
(6, 2),
(7, 1),
(7, 2),
(8, 5),
(9, 5),
(10, 5),
(11, 5),
(12, 5),
(13, 3),
(14, 3),
(15, 3),
(16, 3),
(17, 3);

-- --------------------------------------------------------

--
-- Structure de la table `products_options`
--

DROP TABLE IF EXISTS `products_options`;
CREATE TABLE IF NOT EXISTS `products_options` (
  `products_id` int NOT NULL,
  `options_id` int NOT NULL,
  PRIMARY KEY (`products_id`,`options_id`),
  KEY `IDX_B0800DA6C8A81A9` (`products_id`),
  KEY `IDX_B0800DA3ADB05F1` (`options_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `products_options`
--

INSERT INTO `products_options` (`products_id`, `options_id`) VALUES
(3, 1),
(3, 2),
(3, 3),
(3, 4);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_date` datetime NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `name`, `address`, `postal_code`, `city`, `phone`, `registration_date`, `first_name`) VALUES
(2, 'jeanserrien@test.fr', '[]', '$2y$13$1tywrCzKq3uADjfvwmv/Tu94KKdRzJKbRAIbA93dAc8WAhbytvFdG', 'Serrien', '2 chemin de la montagne', '69000', 'Lyon', NULL, '2025-12-25 21:40:51', 'Jean'),
(3, 'simon@test.fr', '[\"ROLE_USER\", \"ROLE_ADMIN\", \"ROLE_SELLER\"]', '$2y$13$iWGOtLIyyN8Txyo41IAOKuQfULpdsEgjp83eaowSHNfg9KVz9V5y.', 'Thonneau', '10 rue du test', '12345', 'Paris', NULL, '2025-12-28 13:40:56', 'Simon');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `FK_D4E6F81A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_E52FFDEE79D0C0E4` FOREIGN KEY (`billing_address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_E52FFDEEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_E52FFDEEEBF23851` FOREIGN KEY (`delivery_address_id`) REFERENCES `address` (`id`);

--
-- Contraintes pour la table `order_lines`
--
ALTER TABLE `order_lines`
  ADD CONSTRAINT `FK_CC9FF86B4584665A` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `FK_CC9FF86BCFFE9AD6` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`);

--
-- Contraintes pour la table `products_categories`
--
ALTER TABLE `products_categories`
  ADD CONSTRAINT `FK_E8ACBE766C8A81A9` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E8ACBE76A21214B7` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `products_options`
--
ALTER TABLE `products_options`
  ADD CONSTRAINT `FK_B0800DA3ADB05F1` FOREIGN KEY (`options_id`) REFERENCES `options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B0800DA6C8A81A9` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
