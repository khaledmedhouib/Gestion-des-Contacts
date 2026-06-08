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
-- Sample data (extended)
INSERT INTO `contacts` (`nom`, `prenom`, `telephone`, `email`) VALUES

('Dupont',   'Jean',     '0612345678', 'jean.dupont@email.com'),
('Martin',   'Sophie',   '0698765432', 'sophie.martin@email.com'),
('Bernard',  'Lucas',    '0654321098', 'lucas.bernard@email.com'),
('Leroy',    'Emma',     '0623456789', 'emma.leroy@email.com'),
('Moreau',   'Thomas',   '0687654321', 'thomas.moreau@email.com'),

('Fahri',    'Amine',    '20123456',   'amine.fahri@email.com'),
('Ben Ali',  'Yasmine',  '22111222',   'yasmine.benali@email.com'),
('Trabelsi', 'Omar',     '98765432',   'omar.trabelsi@email.com'),
('Haddad',   'Nour',     '91234567',   'nour.haddad@email.com'),
('Khelifi',  'Ali',      '94567812',   'ali.khelifi@email.com'),

('Gharbi',   'Sara',     '20112233',   'sara.gharbi@email.com'),
('Mansouri', 'Khaled',   '22334455',   'khaled.mansouri@email.com'),
('Saidi',    'Rania',    '99887766',   'rania.saidi@email.com'),
('Ben Amor', 'Youssef',  '22331144',   'youssef.benamor@email.com'),
('Jaziri',   'Malek',    '55667788',   'malek.jaziri@email.com'),

('Chebbi',   'Hana',     '66778899',   'hana.chebbi@email.com'),
('Hassen',   'Mehdi',    '77889900',   'mehdi.hassen@email.com'),
('Boughanmi','Ines',     '99001122',   'ines.boughanmi@email.com'),
('Rekik',    'Firas',    '11223344',   'firas.rekik@email.com'),
('Masmoudi', 'Salma',    '55660011',   'salma.masmoudi@email.com'),

('Zribi',    'Aymen',    '66770022',   'aymen.zribi@email.com'),
('Bennour',  'Hiba',     '77880033',   'hiba.bennour@email.com'),
('Guediche', 'Anis',     '88990044',   'anis.guediche@email.com'),
('Sassi',    'Marwa',    '99002211',   'marwa.sassi@email.com'),
('Tlili',    'Rami',     '10101010',   'rami.tlili@email.com');
