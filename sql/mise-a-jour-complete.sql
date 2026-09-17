-- ============================================================
-- SenMoringa — SCRIPT DE MISE À JOUR COMPLET (tout-en-un)
-- ============================================================
-- Ce script remplace TOUS les anciens fichiers "mise-a-jour-*.sql".
-- Il peut être exécuté sur une base SenMoringa déjà installée,
-- quel que soit son état actuel (même si certaines mises à jour
-- précédentes ont déjà été appliquées, ou aucune).
--
-- Il est SANS DANGER à ré-exécuter plusieurs fois : chaque étape
-- vérifie l'état avant d'agir, aucun doublon ne sera créé et
-- aucune commande déjà passée ne sera perdue.
--
-- Utilisation : Import dans phpMyAdmin, sur la base senmoringa.
-- ============================================================


-- ------------------------------------------------------------
-- 1) Colonne d'ordre d'affichage des produits
-- ------------------------------------------------------------
ALTER TABLE produits ADD COLUMN IF NOT EXISTS ordre INT NOT NULL DEFAULT 0;


-- ------------------------------------------------------------
-- 2) Table de protection anti-brute-force sur la connexion admin
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_tentatives_connexion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adresse_ip VARCHAR(45) NOT NULL UNIQUE,
    tentatives INT NOT NULL DEFAULT 1,
    derniere_tentative TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bloque_jusqua TIMESTAMP NULL
) ENGINE=InnoDB;


-- ------------------------------------------------------------
-- 3) Produit POUDRE : s'assure qu'il existe, avec le bon texte
-- ------------------------------------------------------------
INSERT INTO produits (nom, slug, description, description_courte, prix, ancien_prix, image, stock, categorie_id, populaire, actif, ordre)
SELECT * FROM (SELECT
    'Poudre de Moringa 50g' AS nom, 'poudre-moringa-50g' AS slug,
    'La poudre de moringa est un produit 100 % naturel, obtenu à partir de feuilles soigneusement sélectionnées et transformées. Riche en nutriments essentiels, elle contribue à diversifier l\'alimentation et s\'intègre facilement dans les habitudes quotidiennes. Elle peut être ajoutée à l\'eau, aux jus, smoothies, soupes, sauces ou autres préparations culinaires. Pratique et polyvalente, elle permet de profiter des qualités nutritionnelles du moringa tout en valorisant les ressources agricoles sénégalaises. Adoptez la poudre de moringa SenMoringa et faites le choix d\'une alimentation naturelle, nutritive et locale.' AS description,
    'Format découverte, 100% naturel' AS description_courte,
    1500.00 AS prix, NULL AS ancien_prix, 'poudre-moringa-50g.jpg' AS image, 50 AS stock,
    (SELECT id FROM categories WHERE slug = 'poudre') AS categorie_id, 1 AS populaire, 1 AS actif, 1 AS ordre
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM produits WHERE slug = 'poudre-moringa-50g');

-- Met à jour le texte + le prix même si le produit existait déjà
UPDATE produits SET
    description = 'La poudre de moringa est un produit 100 % naturel, obtenu à partir de feuilles soigneusement sélectionnées et transformées. Riche en nutriments essentiels, elle contribue à diversifier l\'alimentation et s\'intègre facilement dans les habitudes quotidiennes. Elle peut être ajoutée à l\'eau, aux jus, smoothies, soupes, sauces ou autres préparations culinaires. Pratique et polyvalente, elle permet de profiter des qualités nutritionnelles du moringa tout en valorisant les ressources agricoles sénégalaises. Adoptez la poudre de moringa SenMoringa et faites le choix d\'une alimentation naturelle, nutritive et locale.',
    prix = 1500.00,
    populaire = 1,
    actif = 1,
    ordre = 1
WHERE slug = 'poudre-moringa-50g';


-- ------------------------------------------------------------
-- 4) Produit HUILE : passage au format réel 30ml / 2500 FCFA
--    (gère tous les anciens noms possibles : vierge 100ml,
--    pousse-cheveux 100ml, ou déjà 30ml)
-- ------------------------------------------------------------
UPDATE produits SET
    nom = 'Huile Pousse-Cheveux au Moringa 30ml',
    slug = 'huile-pousse-cheveux-moringa-30ml',
    description = 'L\'huile capillaire, à base de moringa, d\'olive et de romarin, est un soin naturel conçu pour nourrir et hydrater les cheveux et le cuir chevelu. Sa formule aide à maintenir la douceur, la souplesse et la brillance des cheveux, tout en leur apportant un soin quotidien. Elle peut être utilisée pour les massages du cuir chevelu, les bains d\'huile ou l\'entretien des longueurs. Offrez à vos cheveux un soin naturel et nourrissant avec SenMoringa.',
    description_courte = 'Vitamine C & E — Olive, Nébéday, Romarin',
    prix = 2500.00,
    image = 'huile-pousse-cheveux-moringa-30ml.jpg',
    populaire = 1,
    actif = 1,
    ordre = 20
WHERE slug IN ('huile-moringa-vierge-100ml', 'huile-pousse-cheveux-moringa-100ml', 'huile-pousse-cheveux-moringa-30ml');

-- Si aucune huile n'existait du tout, on la crée
INSERT INTO produits (nom, slug, description, description_courte, prix, ancien_prix, image, stock, categorie_id, populaire, actif, ordre)
SELECT * FROM (SELECT
    'Huile Pousse-Cheveux au Moringa 30ml' AS nom, 'huile-pousse-cheveux-moringa-30ml' AS slug,
    'L\'huile capillaire, à base de moringa, d\'olive et de romarin, est un soin naturel conçu pour nourrir et hydrater les cheveux et le cuir chevelu. Sa formule aide à maintenir la douceur, la souplesse et la brillance des cheveux, tout en leur apportant un soin quotidien. Elle peut être utilisée pour les massages du cuir chevelu, les bains d\'huile ou l\'entretien des longueurs. Offrez à vos cheveux un soin naturel et nourrissant avec SenMoringa.' AS description,
    'Vitamine C & E — Olive, Nébéday, Romarin' AS description_courte,
    2500.00 AS prix, NULL AS ancien_prix, 'huile-pousse-cheveux-moringa-30ml.jpg' AS image, 25 AS stock,
    (SELECT id FROM categories WHERE slug = 'huile') AS categorie_id, 1 AS populaire, 1 AS actif, 20 AS ordre
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM produits WHERE slug = 'huile-pousse-cheveux-moringa-30ml');


-- ------------------------------------------------------------
-- 5) Produits JUS : les 4 vraies variantes à 400 FCFA
-- ------------------------------------------------------------
SET @cat_jus = (SELECT id FROM categories WHERE slug = 'jus' LIMIT 1);

-- On retire l'ancien jus générique s'il traîne encore
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

-- S'assure que le prix des 4 jus est bien 400 FCFA, même s'ils existaient déjà à un autre prix
UPDATE produits SET prix = 400.00
WHERE slug IN ('nebedaye-250ml', 'nebedays-bissap-250ml', 'nebedays-bouye-250ml', 'nebedays-fraise-250ml');


-- ------------------------------------------------------------
-- 6) Nettoyage : on ne garde que les 6 produits actuels du site
--    (les commandes déjà passées restent intactes : leur lien
--    vers le produit passe simplement à NULL, mais le nom et le
--    prix figurent déjà en clair dans chaque ligne de commande).
-- ------------------------------------------------------------
DELETE FROM produits
WHERE slug NOT IN (
    'poudre-moringa-50g',
    'huile-pousse-cheveux-moringa-30ml',
    'nebedaye-250ml',
    'nebedays-bissap-250ml',
    'nebedays-bouye-250ml',
    'nebedays-fraise-250ml'
);

-- On retire la catégorie "Compléments", qui n'a plus aucun produit
DELETE FROM categories WHERE slug = 'complements';
