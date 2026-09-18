-- ============================================================
-- SenMoringa — Base de données (fichier unique)
-- ============================================================
-- Ce fichier unique remplace tous les anciens scripts séparés
-- (mise-a-jour-complete.sql, mise-a-jour-2026-09-temoignages.sql...).
--
-- Il fonctionne dans les DEUX cas, sans aucune casse :
--   • Nouvelle installation (base vide) → crée tout et installe
--     les produits, catégories, témoignages et le compte admin
--     de démarrage.
--   • Base déjà existante → met simplement à jour les tables et
--     les produits qui doivent l'être. Vos commandes, votre
--     compte admin (et son mot de passe s'il a été changé), et
--     vos témoignages déjà personnalisés ne sont JAMAIS touchés.
--
-- SANS DANGER à importer plusieurs fois : chaque étape vérifie
-- l'état avant d'agir.
--
-- Utilisation : phpMyAdmin > onglet SQL > coller tout ce fichier
-- > Exécuter. (Ou : mysql -u root --default-character-set=utf8mb4 < senmoringa.sql)
-- ============================================================

CREATE DATABASE IF NOT EXISTS senmoringa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE senmoringa;


-- ============================================================
-- 1) STRUCTURE DES TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produits (
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

-- Rattrapage pour les bases créées avant l'ajout de la colonne "ordre"
ALTER TABLE produits ADD COLUMN IF NOT EXISTS ordre INT NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS commandes (
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

CREATE TABLE IF NOT EXISTS commande_produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT DEFAULT NULL,
    nom_produit VARCHAR(150) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    quantite INT NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Protection anti-brute-force sur la connexion admin
CREATE TABLE IF NOT EXISTS admin_tentatives_connexion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adresse_ip VARCHAR(45) NOT NULL UNIQUE,
    tentatives INT NOT NULL DEFAULT 1,
    derniere_tentative TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bloque_jusqua TIMESTAMP NULL
) ENGINE=InnoDB;

-- Témoignages / avis clients affichés sur la page d'accueil
CREATE TABLE IF NOT EXISTS temoignages (
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
-- 2) CATÉGORIES (créées seulement si absentes, jamais modifiées
--    si elles existent déjà)
-- ============================================================
INSERT IGNORE INTO categories (nom, slug, description) VALUES
('Poudre', 'poudre', 'Poudre de feuilles de moringa séchées et broyées'),
('Huile', 'huile', 'Huile pressée à froid à partir des graines de moringa'),
('Jus', 'jus', 'Jus et boissons à base de moringa');

-- La catégorie "Compléments" n'est plus utilisée : on la retire si elle traîne encore
DELETE FROM categories WHERE slug = 'complements';


-- ============================================================
-- 3) PRODUITS : crée ceux qui manquent, corrige le texte/prix
--    de ceux qui existent déjà, retire les anciens produits de
--    démo qui ne sont plus vendus.
-- ============================================================

-- --- Poudre de Moringa 50g ---
INSERT INTO produits (nom, slug, description, description_courte, prix, ancien_prix, image, stock, categorie_id, populaire, actif, ordre)
SELECT * FROM (SELECT
    'Poudre de Moringa 50g' AS nom, 'poudre-moringa-50g' AS slug,
    'La poudre de moringa est un produit 100 % naturel, obtenu à partir de feuilles soigneusement sélectionnées et transformées. Riche en nutriments essentiels, elle contribue à diversifier l\'alimentation et s\'intègre facilement dans les habitudes quotidiennes. Elle peut être ajoutée à l\'eau, aux jus, smoothies, soupes, sauces ou autres préparations culinaires. Pratique et polyvalente, elle permet de profiter des qualités nutritionnelles du moringa tout en valorisant les ressources agricoles sénégalaises. Adoptez la poudre de moringa SenMoringa et faites le choix d\'une alimentation naturelle, nutritive et locale.' AS description,
    'Format découverte, 100% naturel' AS description_courte,
    1500.00 AS prix, NULL AS ancien_prix, 'poudre-moringa-50g.jpg' AS image, 50 AS stock,
    (SELECT id FROM categories WHERE slug = 'poudre') AS categorie_id, 1 AS populaire, 1 AS actif, 1 AS ordre
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM produits WHERE slug = 'poudre-moringa-50g');

UPDATE produits SET
    description = 'La poudre de moringa est un produit 100 % naturel, obtenu à partir de feuilles soigneusement sélectionnées et transformées. Riche en nutriments essentiels, elle contribue à diversifier l\'alimentation et s\'intègre facilement dans les habitudes quotidiennes. Elle peut être ajoutée à l\'eau, aux jus, smoothies, soupes, sauces ou autres préparations culinaires. Pratique et polyvalente, elle permet de profiter des qualités nutritionnelles du moringa tout en valorisant les ressources agricoles sénégalaises. Adoptez la poudre de moringa SenMoringa et faites le choix d\'une alimentation naturelle, nutritive et locale.',
    prix = 1500.00, populaire = 1, actif = 1, ordre = 1
WHERE slug = 'poudre-moringa-50g';

-- --- Huile Pousse-Cheveux 30ml (gère les anciens noms : vierge 100ml, pousse-cheveux 100ml) ---
UPDATE produits SET
    nom = 'Huile Pousse-Cheveux au Moringa 30ml',
    slug = 'huile-pousse-cheveux-moringa-30ml',
    description = 'L\'huile capillaire, à base de moringa, d\'olive et de romarin, est un soin naturel conçu pour nourrir et hydrater les cheveux et le cuir chevelu. Sa formule aide à maintenir la douceur, la souplesse et la brillance des cheveux, tout en leur apportant un soin quotidien. Elle peut être utilisée pour les massages du cuir chevelu, les bains d\'huile ou l\'entretien des longueurs. Offrez à vos cheveux un soin naturel et nourrissant avec SenMoringa.',
    description_courte = 'Vitamine C & E — Olive, Nébéday, Romarin',
    prix = 2500.00, image = 'huile-pousse-cheveux-moringa-30ml.jpg', populaire = 1, actif = 1, ordre = 20
WHERE slug IN ('huile-moringa-vierge-100ml', 'huile-pousse-cheveux-moringa-100ml', 'huile-pousse-cheveux-moringa-30ml');

INSERT INTO produits (nom, slug, description, description_courte, prix, ancien_prix, image, stock, categorie_id, populaire, actif, ordre)
SELECT * FROM (SELECT
    'Huile Pousse-Cheveux au Moringa 30ml' AS nom, 'huile-pousse-cheveux-moringa-30ml' AS slug,
    'L\'huile capillaire, à base de moringa, d\'olive et de romarin, est un soin naturel conçu pour nourrir et hydrater les cheveux et le cuir chevelu. Sa formule aide à maintenir la douceur, la souplesse et la brillance des cheveux, tout en leur apportant un soin quotidien. Elle peut être utilisée pour les massages du cuir chevelu, les bains d\'huile ou l\'entretien des longueurs. Offrez à vos cheveux un soin naturel et nourrissant avec SenMoringa.' AS description,
    'Vitamine C & E — Olive, Nébéday, Romarin' AS description_courte,
    2500.00 AS prix, NULL AS ancien_prix, 'huile-pousse-cheveux-moringa-30ml.jpg' AS image, 25 AS stock,
    (SELECT id FROM categories WHERE slug = 'huile') AS categorie_id, 1 AS populaire, 1 AS actif, 20 AS ordre
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM produits WHERE slug = 'huile-pousse-cheveux-moringa-30ml');

-- --- Les 4 jus (250ml, 400 FCFA) ---
SET @cat_jus = (SELECT id FROM categories WHERE slug = 'jus' LIMIT 1);

DELETE FROM produits WHERE slug = 'jus-moringa-frais-1l';

INSERT INTO produits (nom, slug, description, description_courte, prix, image, stock, categorie_id, populaire, actif, ordre)
SELECT * FROM (SELECT
    'Nébédaye 250ml' AS nom, 'nebedaye-250ml' AS slug,
    'Notre jus pur à 100% de nébéday (moringa), pressé à froid sans aucun ajout. Le concentré brut de tous les bienfaits de la feuille.' AS description,
    'Nébéday / Moringa 100%' AS description_courte,
    400.00 AS prix, 'nebedaye-250ml.jpg' AS image, 20 AS stock, @cat_jus AS categorie_id, 1 AS populaire, 1 AS actif, 10 AS ordre
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM produits WHERE slug = 'nebedaye-250ml');

INSERT INTO produits (nom, slug, description, description_courte, prix, image, stock, categorie_id, populaire, actif, ordre)
SELECT * FROM (SELECT
    'Nébéday\'s Bissap 250ml' AS nom, 'nebedays-bissap-250ml' AS slug,
    'Notre jus de bissap (hibiscus) relevé au nébéday, pour un mélange gourmand et acidulé qui garde toute la richesse du moringa.' AS description,
    'Bissap 75% — Nébéday / Moringa 25%' AS description_courte,
    400.00 AS prix, 'nebedays-bissap-250ml.jpg' AS image, 20 AS stock, @cat_jus AS categorie_id, 1 AS populaire, 1 AS actif, 11 AS ordre
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM produits WHERE slug = 'nebedays-bissap-250ml');

INSERT INTO produits (nom, slug, description, description_courte, prix, image, stock, categorie_id, populaire, actif, ordre)
SELECT * FROM (SELECT
    'Nébéday\'s Bouye 250ml' AS nom, 'nebedays-bouye-250ml' AS slug,
    'Notre jus de bouye (pain de singe / baobab) enrichi au nébéday, pour une boisson onctueuse et typiquement sénégalaise.' AS description,
    'Bouye 75% — Nébéday / Moringa 25%' AS description_courte,
    400.00 AS prix, 'nebedays-bouye-250ml.jpg' AS image, 20 AS stock, @cat_jus AS categorie_id, 0 AS populaire, 1 AS actif, 12 AS ordre
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM produits WHERE slug = 'nebedays-bouye-250ml');

INSERT INTO produits (nom, slug, description, description_courte, prix, image, stock, categorie_id, populaire, actif, ordre)
SELECT * FROM (SELECT
    'Nébéday\'s Fraise 250ml' AS nom, 'nebedays-fraise-250ml' AS slug,
    'Notre jus de fraise adouci et enrichi au nébéday, pour une version fruitée et douce du moringa, appréciée des enfants comme des adultes.' AS description,
    'Fraise 75% — Nébéday / Moringa 25%' AS description_courte,
    400.00 AS prix, 'nebedays-fraise-250ml.jpg' AS image, 20 AS stock, @cat_jus AS categorie_id, 0 AS populaire, 1 AS actif, 13 AS ordre
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM produits WHERE slug = 'nebedays-fraise-250ml');

UPDATE produits SET prix = 400.00
WHERE slug IN ('nebedaye-250ml', 'nebedays-bissap-250ml', 'nebedays-bouye-250ml', 'nebedays-fraise-250ml');

-- --- Nettoyage : ne garder que les 6 produits actuellement vendus ---
-- (les commandes déjà passées restent intactes : le nom et le prix
-- de chaque article sont déjà enregistrés en clair dans la commande)
DELETE FROM produits
WHERE slug NOT IN (
    'poudre-moringa-50g',
    'huile-pousse-cheveux-moringa-30ml',
    'nebedaye-250ml',
    'nebedays-bissap-250ml',
    'nebedays-bouye-250ml',
    'nebedays-fraise-250ml'
);


-- ============================================================
-- 4) TÉMOIGNAGES : ajoutés uniquement si la table est encore
--    vide (ne touche jamais à des témoignages déjà personnalisés
--    depuis l'espace admin)
-- ============================================================
INSERT INTO temoignages (nom, ville, note, texte, actif, ordre)
SELECT * FROM (SELECT
    'Aïssatou Diop' AS nom, 'Dakar' AS ville, 5 AS note,
    'La poudre de moringa se dissout bien dans mon bouillie du matin, et je sens vraiment la différence sur mon énergie depuis que j''en prends.' AS texte,
    1 AS actif, 1 AS ordre
UNION ALL SELECT
    'Mariama Ba', 'Thiès', 5,
    'L''huile pousse-cheveux au moringa a changé l''état de mon cuir chevelu en quelques semaines. Le flacon de 30ml est petit mais dure longtemps grâce à la pipette.',
    1, 2
UNION ALL SELECT
    'Ousmane Fall', 'Saint-Louis', 5,
    'Le Nébédaye au bissap est top, pas trop sucré et on sent vraiment le moringa. Commande reçue en deux jours, bien emballée.',
    1, 3
UNION ALL SELECT
    'Khady Sow', 'Mbour', 4,
    'Le jus à la fraise plaît beaucoup à mes enfants, c''est doux et naturel. Le format 250ml est parfait pour la journée.',
    1, 4
UNION ALL SELECT
    'Ibrahima Ndiaye', 'Dakar', 5,
    'Le nébédaye au bouye a un goût très authentique, ça me rappelle les jus de mon enfance. Livraison rapide via WhatsApp, très pratique.',
    1, 5
) AS depart
WHERE NOT EXISTS (SELECT 1 FROM temoignages);


-- ============================================================
-- 5) COMPTE ADMIN : créé uniquement s'il n'existe aucun compte
--    (ne réinitialise jamais un mot de passe déjà changé)
--    Identifiant par défaut : admin / Mot de passe : SenMoringa2026!
-- ============================================================
INSERT IGNORE INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$HD6AJSuTtVa9pHIRvr7Cm.Qszg8Af/x4SGOvXU33oaiG7EI5FU48y');
