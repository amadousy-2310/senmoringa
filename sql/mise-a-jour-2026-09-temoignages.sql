-- ============================================================
-- Script de mise à jour à exécuter sur une base SenMoringa
-- déjà installée (via phpMyAdmin par ex.).
-- Objectif : ajouter le système de témoignages clients,
-- gérable depuis l'espace admin (menu "Témoignages").
-- ============================================================

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

-- Quelques témoignages réalistes de départ, basés sur les vrais produits.
-- Tu peux les modifier/supprimer et en ajouter d'autres directement
-- depuis l'espace admin > Témoignages.
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
