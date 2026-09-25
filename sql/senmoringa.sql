-- ============================================================
-- SenMoringa — Schéma de base de données
-- Importer ce fichier dans phpMyAdmin (ou via la commande mysql)
-- ============================================================

CREATE DATABASE IF NOT EXISTS senmoringa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE senmoringa;

-- ---------------------------------------
-- Catégories de produits
-- ---------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

-- ---------------------------------------
-- Produits
-- ---------------------------------------
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    description_courte VARCHAR(255),
    prix DECIMAL(10,2) NOT NULL,
    ancien_prix DECIMAL(10,2) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    stock INT NOT NULL DEFAULT 0,
    categorie_id INT DEFAULT NULL,
    populaire TINYINT(1) NOT NULL DEFAULT 0,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    ordre INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------
-- Commandes
-- ---------------------------------------
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(20) NOT NULL UNIQUE,
    nom_client VARCHAR(150) NOT NULL,
    telephone VARCHAR(30) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    adresse VARCHAR(255) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    mode_paiement VARCHAR(50) NOT NULL,
    note TEXT,
    total DECIMAL(10,2) NOT NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------
-- Lignes de commande
-- ---------------------------------------
CREATE TABLE commande_produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT DEFAULT NULL,
    nom_produit VARCHAR(150) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    quantite INT NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------
-- Administrateurs
-- ---------------------------------------
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Suivi des tentatives de connexion échouées (protection anti-brute-force)
CREATE TABLE admin_tentatives_connexion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adresse_ip VARCHAR(45) NOT NULL UNIQUE,
    tentatives INT NOT NULL DEFAULT 1,
    derniere_tentative TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bloque_jusqua TIMESTAMP NULL
) ENGINE=InnoDB;

-- Témoignages / avis clients affichés sur la page d'accueil
CREATE TABLE temoignages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ville VARCHAR(100) DEFAULT NULL,
    note TINYINT NOT NULL DEFAULT 5,
    texte TEXT NOT NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    ordre INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DONNÉES DE DÉMARRAGE
-- ============================================================

INSERT INTO categories (nom, slug, description) VALUES
('Poudre', 'poudre', 'Poudre de feuilles de moringa séchées et broyées'),
('Huile', 'huile', 'Huile pressée à froid à partir des graines de moringa'),
('Jus', 'jus', 'Jus et boissons à base de moringa');

INSERT INTO produits (nom, slug, description, description_courte, prix, ancien_prix, image, stock, categorie_id, populaire, actif, ordre) VALUES
('Poudre de Moringa 50g', 'poudre-moringa-50g',
 'La poudre de moringa est un produit 100 % naturel, obtenu à partir de feuilles soigneusement sélectionnées et transformées. Riche en nutriments essentiels, elle contribue à diversifier l\'alimentation et s\'intègre facilement dans les habitudes quotidiennes. Elle peut être ajoutée à l\'eau, aux jus, smoothies, soupes, sauces ou autres préparations culinaires. Pratique et polyvalente, elle permet de profiter des qualités nutritionnelles du moringa tout en valorisant les ressources agricoles sénégalaises. Adoptez la poudre de moringa SenMoringa et faites le choix d\'une alimentation naturelle, nutritive et locale.',
 'Format découverte, 100% naturel', 1500.00, NULL, 'poudre-moringa-50g.jpg', 50, 1, 1, 1, 1),

('Nébédaye 250ml', 'nebedaye-250ml',
 'Notre jus pur à 100% de nébéday (moringa), pressé à froid sans aucun ajout. Le concentré brut de tous les bienfaits de la feuille.',
 'Nébéday / Moringa 100%', 400.00, NULL, 'nebedaye-250ml.jpg', 20, 3, 1, 1, 10),

('Nébéday\'s Bissap 250ml', 'nebedays-bissap-250ml',
 'Notre jus de bissap (hibiscus) relevé au nébéday, pour un mélange gourmand et acidulé qui garde toute la richesse du moringa.',
 'Bissap 75% — Nébéday / Moringa 25%', 400.00, NULL, 'nebedays-bissap-250ml.jpg', 20, 3, 1, 1, 11),

('Nébéday\'s Bouye 250ml', 'nebedays-bouye-250ml',
 'Notre jus de bouye (pain de singe / baobab) enrichi au nébéday, pour une boisson onctueuse et typiquement sénégalaise.',
 'Bouye 75% — Nébéday / Moringa 25%', 400.00, NULL, 'nebedays-bouye-250ml.jpg', 20, 3, 0, 1, 12),

('Nébéday\'s Fraise 250ml', 'nebedays-fraise-250ml',
 'Notre jus de fraise adouci et enrichi au nébéday, pour une version fruitée et douce du moringa, appréciée des enfants comme des adultes.',
 'Fraise 75% — Nébéday / Moringa 25%', 400.00, NULL, 'nebedays-fraise-250ml.jpg', 20, 3, 0, 1, 13),

('Huile Pousse-Cheveux au Moringa 30ml', 'huile-pousse-cheveux-moringa-30ml',
 'L\'huile capillaire, à base de moringa, d\'olive et de romarin, est un soin naturel conçu pour nourrir et hydrater les cheveux et le cuir chevelu. Sa formule aide à maintenir la douceur, la souplesse et la brillance des cheveux, tout en leur apportant un soin quotidien. Elle peut être utilisée pour les massages du cuir chevelu, les bains d\'huile ou l\'entretien des longueurs. Offrez à vos cheveux un soin naturel et nourrissant avec SenMoringa.',
 'Vitamine C & E — Olive, Nébéday, Romarin', 2500.00, NULL, 'huile-pousse-cheveux-moringa-30ml.jpg', 25, 2, 1, 1, 20);

INSERT INTO temoignages (nom, ville, note, texte, actif, ordre) VALUES
('Aïssatou Diop', 'Dakar', 5, 'La poudre de moringa se dissout bien dans mon bouillie du matin, et je sens vraiment la différence sur mon énergie depuis que j\'en prends.', 1, 1),
('Mariama Ba', 'Thiès', 5, 'L\'huile pousse-cheveux au moringa a changé l\'état de mon cuir chevelu en quelques semaines. Le flacon de 30ml est petit mais dure longtemps grâce à la pipette.', 1, 2),
('Ousmane Fall', 'Saint-Louis', 5, 'Le Nébédaye au bissap est top, pas trop sucré et on sent vraiment le moringa. Commande reçue en deux jours, bien emballée.', 1, 3),
('Khady Sow', 'Mbour', 4, 'Le jus à la fraise plaît beaucoup à mes enfants, c\'est doux et naturel. Le format 250ml est parfait pour la journée.', 1, 4),
('Ibrahima Ndiaye', 'Dakar', 5, 'Le nébédaye au bouye a un goût très authentique, ça me rappelle les jus de mon enfance. Livraison rapide via WhatsApp, très pratique.', 1, 5);

-- Compte admin (identifiant = e-mail, mot de passe déjà chiffré ci-dessous)
INSERT INTO admin_users (username, password_hash) VALUES
('Senmoringa11@gmail.com', '$2b$10$lNeRgv/7cthHFqUPsktr3.1Gz3vmJ21rDVLgGRpMPX1vR5CTj4qxm');
