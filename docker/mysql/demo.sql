SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
SET foreign_key_checks=0;
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --
-- User table for demo
-- --

INSERT INTO `cafet_users` (`id`, `username`, `password`, `firstname`, `familyname`, `email`, `phone`, `group_id`, `permissions`) VALUES
(1,  'Nowtryz',     'sha256.Wr0Fy8AGSviXr/BaPYjgoBeEqpshzoxxO6Nih14HVRk=.KVdIXUR5j+nI3vchg2u3ofBNHZzXq+9t9dNdb2sulMQ=', 'Damien', 'Djomby', 'damien.djmb@gmail.com', '0611223344', 4, 'a:3:{s:1:"a";b:1;s:3:"b_a";b:0;s:3:"b_b";b:0;}'),
(2,  'Agassiz',     '', 'Louis',            'Agassiz',     'scientist01@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(3,  'Ampère',      '', 'André-Marie',      'Ampère',      'scientist02@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(4,  'Avogadro',    '', 'Amedeo',           'Avogadro',    'scientist03@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(5,  'Lee',         '', 'Tim',              'Berners-Lee', 'scientist04@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(6,  'Bernoulli',   '', 'Daniel',           'Bernoulli',   'scientist05@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(7,  'Bohr',        '', 'Niels',            'Bohr',        'scientist06@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(8,  'Braun',       '', 'Wernher',          'von Braun',   'scientist07@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(9,  'Celsius',     '', 'Anders',           'Celsius',     'scientist08@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(10, 'Coulomb',     '', 'Charles-Augustin', 'Coulomb',     'scientist09@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(11, 'Darwin',      '', 'Charles',          'Darwin',      'scientist10@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(12, 'Descartes',   '', 'René',             'Descartes',   'scientist11@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(13, 'Einstein',    '', 'Albert',           'Einstein',    'scientist12@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(14, 'Alexandria',  '', 'Euclid of',        'Alexandria',  'scientist13@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(15, 'Euler',       '', 'Leonhard',         'Euler',       'scientist14@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(16, 'Fleming',     '', 'Alexander',        'Fleming',     'scientist15@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(17, 'Hertz',       '', 'Heinrich',         'Hertz',       'scientist16@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(18, 'Hippocrates', '', 'Hippocrates',      'II',          'scientist17@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(19, 'Hopkins',     '', 'William',          'Hopkins',     'scientist18@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(20, 'Hubble',      '', 'Edwin',            'Hubble',      'scientist19@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(21, 'Kepler',      '', 'Johannes',         'Kepler',      'scientist20@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(22, 'Lagrange',    '', 'Joseph-Louis',     'Lagrange',    'scientist21@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(23, 'Lavoisier',   '', 'Antoine',          'Lavoisier',   'scientist22@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(24, 'Leibniz',     '', 'Gottfried',        'Leibniz',     'scientist23@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(25, 'Vinci',       '', 'Leonardo',         'da Vinci',    'scientist24@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(26, 'Maxwell',     '', 'James Clerk',      'Maxwell',     'scientist25@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(27, 'Newton',      '', 'Isaac',            'Newton',      'scientist26@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(28, 'Nobel',       '', 'Alfred',           'Nobel',       'scientist27@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(29, 'Ohm',         '', 'Georg',            'Ohm',         'scientist28@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(30, 'Oppenheimer', '', 'Robert',           'Oppenheimer', 'scientist29@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(31, 'Pascal',      '', 'Blaise',           'Pascal',      'scientist30@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(32, 'Pasteur',     '', 'Louis',            'Pasteur',     'scientist31@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(33, 'Pauling',     '', 'Linus',            'Pauling',     'scientist32@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(34, 'Potter',      '', 'Harry',            'Potter',      'scientist33@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(35, 'Pythagoras',  '', 'Damien',           'Pythagoras',  'scientist34@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(36, 'Riemann',     '', 'Bernhard',         'Riemann',     'scientist35@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(37, 'Tesla',       '', 'Nikola',           'Tesla',       'scientist36@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(38, 'Linux',       '', 'Linus',            'Torvalds',    'scientist37@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(39, 'Turing',      '', 'Alan',             'Turing',      'scientist38@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(40, 'Volta',       '', 'Alessandro',       'Volta',       'scientist39@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}'),
(41, 'Wright',      '', 'Orville',          'Wright',      'scientist40@no.dom',      '0611223344', 1, 'a:1:{s:3:"b_c";b:0;}');

INSERT INTO `cafet_customers` (`id`, `user_id`, `balance`) VALUES
(1 , 1 , 65.5),
(2 , 2 , 20),(3 , 3 , 20),(4 , 4 , 20),(5 , 5 , 20),
(6 , 6 , 20),(7 , 7 , 20),(8 , 8 , 20),(9 , 9 , 20),
(10, 10, 20),(11, 11, 20),(12, 12, 20),(13, 13, 20),
(14, 14, 20),(15, 15, 20),(16, 16, 20),(17, 17, 20),
(18, 18, 20),(19, 19, 20),(20, 20, 20),(21, 21, 20),
(22, 22, 20),(23, 23, 20),(24, 24, 20),(25, 25, 20),
(26, 26, 20),(27, 27, 20),(28, 28, 20),(29, 29, 20),
(30, 30, 20),(31, 31, 20),(32, 32, 20),(33, 33, 20),
(34, 34, 20),(35, 35, 20),(36, 36, 20),(37, 37, 20),
(38, 38, 20),(39, 39, 20),(40, 40, 20),(41, 41, 20);
-- --
-- products_groups table for demo
-- --
INSERT INTO `cafet_products_groups` (`id`, `name`, `display_name`, `edit`) VALUES
(1, 'Cold drinks', 'Cold drinks (0,70€)', '2018-01-16 21:28:40'),
(2, 'Hot drinks',  'Hot drinks (0,30€)',  '2018-01-16 21:29:30'),
(3, 'Sweetmeat',   'Sweetmeat (0,80€)',   '2018-01-16 21:29:48'),
(4, 'Sandwiches',  'Sandwiches',          '2017-11-08 11:08:47');

-- --
-- products_edits table for demo
-- --
INSERT INTO `cafet_products_edits` (`id`, `product`, `name`, `price`, `edit`) VALUES
(1,  1,  'Coca-Cola',        0.70, '2018-09-14 16:00:00'),
(2,  2,  'Fanta',            0.70, '2018-09-14 16:00:00'),
(3,  3,  'Orangina',         0.70, '2018-09-14 16:00:00'),
(4,  4,  'Ice Tea',          0.70, '2018-09-14 16:00:00'),
(5,  5,  'Red Bull',         0.70, '2018-09-14 16:00:00'),
(6,  6,  'Schweppes',        0.70, '2018-09-14 16:00:00'),
(7,  7,  'Sanpellegrino',    0.70, '2018-09-14 16:00:00'),
(8,  8,  'Coca-Cola Cherry', 0.70, '2018-09-14 16:00:00'),
(9,  9,  'Café',             0.30, '2018-09-14 16:00:00'),
(10, 10, 'Thé',              0.30, '2018-09-14 16:00:00'),
(11, 11, 'Chocolat chaud',   0.30, '2018-09-14 16:00:00'),
(12, 12, 'Mars',             0.80, '2018-09-14 16:00:00'),
(13, 13, 'Kit Kat x2',       0.80, '2018-09-14 16:00:00'),
(14, 14, 'Skittles',         0.80, '2018-09-14 16:00:00'),
(15, 15, 'Snickers',         0.80, '2018-09-14 16:00:00'),
(16, 16, "M&M's",            0.80, '2018-09-14 16:00:00'),
(17, 17, 'Kinder Bueno',     0.80, '2018-09-14 16:00:00'),
(18, 18, 'Tuna',             4.50, '2018-09-14 16:00:00'),
(19, 19, 'Ham',              4.50, '2018-09-14 16:00:00');

-- --
-- products table for demo
-- --
INSERT INTO `cafet_products` (`id`, `product_group`, `image`, `stock`, `viewable`, `last_edit`) VALUES
(1,  1, '', 100, 1, 1),
(2,  1, '', 100, 1, 2),
(3,  1, '', 100, 1, 3),
(4,  1, '', 100, 1, 4),
(5,  1, '', 100, 1, 5),
(6,  1, '', 100, 1, 6),
(7,  1, '', 100, 1, 7),
(8,  1, '', 100, 1, 8),
(9,  2, '', 100, 1, 9),
(10, 2, '', 100, 1, 10),
(11, 2, '', 100, 1, 11),
(12, 3, '', 100, 1, 12),
(13, 3, '', 100, 1, 13),
(14, 3, '', 100, 1, 14),
(15, 3, '', 100, 1, 15),
(16, 3, '', 100, 1, 16),
(17, 3, '', 100, 1, 17),
(18, 4, '', 100, 1, 18),
(19, 4, '', 100, 1, 19);

-- --
-- Auto increments
-- --

ALTER TABLE `cafet_users` AUTO_INCREMENT=42;
ALTER TABLE `cafet_products_groups` AUTO_INCREMENT=5;
ALTER TABLE `cafet_products` AUTO_INCREMENT=20;
ALTER TABLE `cafet_products_edits` AUTO_INCREMENT=20;

-- --
-- End
--
SET foreign_key_checks=1;
COMMIT;