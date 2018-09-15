SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --
-- User table for demo
-- --
INSERT INTO `users` (`id`, `Pseudo`, `MDP`, `Annee`, `SU`, `admin`, `cafet`, `res_cafet`, `adm_cafet`, `Comm`, `Prenom`, `Nom`, `Email`, `Tel`, `actif`, `adherent`, `Credit`, `log`, `online`, `regkey`) VALUES
(1,  'Nowtryz',     '7313e2f55aec99be5473ab80086d88a1d1f6998a', 2016, 0, 1, 1, 1, 1, 1, 'DJOMBY',           'DAMIEN',      'damien.djmb@gmail.com', '0611223344', 3, 1, 67.5, '2018-01-24 13:50:21', 0, '0'),
(2,  'Agassiz',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'Louis',            'Agassiz',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(3,  'Ampère',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'André-Marie',      'Ampère',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(4,  'Avogadro',    '',                                         2018, 0, 0, 1, 0, 0, 0, 'Amedeo',           'Avogadro',    'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(5,  'Lee',         '',                                         2018, 0, 0, 1, 0, 0, 0, 'Tim',              'Berners-Lee', 'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(6,  'Bernoulli',   '',                                         2018, 0, 0, 1, 0, 0, 0, 'Daniel',           'Bernoulli',   'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(7,  'Bohr',        '',                                         2018, 0, 0, 1, 0, 0, 0, 'Niels',            'Bohr',        'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(8,  'Braun',       '',                                         2018, 0, 0, 1, 0, 0, 0, 'Wernher',          'von Braun',   'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(9,  'Celsius',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'Anders',           'Celsius',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(10, 'Coulomb',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'Charles-Augustin', 'Coulomb',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(11, 'Darwin',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'Charles',          'Darwin',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(12, 'Descartes',   '',                                         2018, 0, 0, 1, 0, 0, 0, 'René',             'Descartes',   'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(13, 'Einstein',    '',                                         2018, 0, 0, 1, 0, 0, 0, 'Albert',           'Einstein',    'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(14, 'Alexandria',  '',                                         2018, 0, 0, 1, 0, 0, 0, 'Euclid of',        'Alexandria',  'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(15, 'Euler',       '',                                         2018, 0, 0, 1, 0, 0, 0, 'Leonhard',         'Euler',       'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(16, 'Fleming',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'Alexander',        'Fleming',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(17, 'Hertz',       '',                                         2018, 0, 0, 1, 0, 0, 0, 'Heinrich',         'Hertz',       'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(18, 'Hippocrates', '',                                         2018, 0, 0, 1, 0, 0, 0, 'Hippocrates',      'II',          'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(19, 'Hopkins',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'William',          'Hopkins',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(20, 'Hubble',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'Edwin',            'Hubble',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(21, 'Kepler',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'Johannes',         'Kepler',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(22, 'Lagrange',    '',                                         2018, 0, 0, 1, 0, 0, 0, 'Joseph-Louis',     'Lagrange',    'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(23, 'Lavoisier',   '',                                         2018, 0, 0, 1, 0, 0, 0, 'Antoine',          'Lavoisier',   'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(24, 'Leibniz',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'Gottfried',        'Leibniz',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(25, 'Vinci',       '',                                         2018, 0, 0, 1, 0, 0, 0, 'Leonardo',         'da Vinci',    'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(26, 'Maxwell',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'James Clerk',      'Maxwell',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(27, 'Newton',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'Isaac',            'Newton',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(28, 'Nobel',       '',                                         2018, 0, 0, 1, 0, 0, 0, 'Alfred',           'Nobel',       'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(29, 'Ohm',         '',                                         2018, 0, 0, 1, 0, 0, 0, 'Georg',            'Ohm',         'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(30, 'Oppenheimer', '',                                         2018, 0, 0, 1, 0, 0, 0, 'Robert',           'Oppenheimer', 'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(31, 'Pascal',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'Blaise',           'Pascal',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(32, 'Pasteur',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'Louis',            'Pasteur',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(33, 'Pauling',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'Linus',            'Pauling',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(34, 'Potter',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'Harry',            'Potter',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(35, 'Pythagoras',  '',                                         2018, 0, 0, 1, 0, 0, 0, 'Damien',           'Pythagoras',  'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(36, 'Riemann',     '',                                         2018, 0, 0, 1, 0, 0, 0, 'Bernhard',         'Riemann',     'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(37, 'Tesla',       '',                                         2018, 0, 0, 1, 0, 0, 0, 'Nikola',           'Tesla',       'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(38, 'Linux',       '',                                         2018, 0, 0, 1, 0, 0, 0, 'Linus ',           'Torvalds',    'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(39, 'Turing',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'Alan',             'Turing',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(40, 'Volta',       '',                                         2018, 0, 0, 1, 0, 0, 0, 'Alessandro',       'Volta',       'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0'),
(41, 'Wright',      '',                                         2018, 0, 0, 1, 0, 0, 0, 'Orville',          'Wright',      'scientist@no.dom',      '0611223344', 0, 0, 20,   '2018-09-14 16:00:00', 0, '0');

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

ALTER TABLE `users` AUTO_INCREMENT=42;
ALTER TABLE `cafet_products_groups` AUTO_INCREMENT=5;
ALTER TABLE `cafet_products` AUTO_INCREMENT=20;
ALTER TABLE `cafet_products_edits` AUTO_INCREMENT=20;

-- --
-- End
--
COMMIT;