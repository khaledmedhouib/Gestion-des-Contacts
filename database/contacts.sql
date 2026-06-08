-- =============================================
-- Gestion des Contacts Personnels
-- Database: contact_manager
-- =============================================

CREATE DATABASE IF NOT EXISTS `contact_manager`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `contact_manager`;

DROP TABLE IF EXISTS `contacts`;

CREATE TABLE `contacts` (
  `id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `nom`         VARCHAR(50)   NOT NULL,
  `prenom`      VARCHAR(50)   NOT NULL,
  `telephone`   VARCHAR(20)   NOT NULL,
  `email`       VARCHAR(100)  NOT NULL,
  `photo`       VARCHAR(255)  DEFAULT NULL,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data
INSERT INTO `contacts` (`nom`, `prenom`, `telephone`, `email`) VALUES
('Dupont',   'Jean',    '0612345678', 'jean.dupont@email.com'),
('Martin',   'Sophie',  '0698765432', 'sophie.martin@email.com'),
('Bernard',  'Lucas',   '0654321098', 'lucas.bernard@email.com'),
('Leroy',    'Emma',    '0623456789', 'emma.leroy@email.com'),
('Moreau',   'Thomas',  '0687654321', 'thomas.moreau@email.com');
