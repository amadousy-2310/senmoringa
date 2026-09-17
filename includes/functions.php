<?php
/**
 * SenMoringa — Fonctions utilitaires
 */

function formatPrix($nombre): string
{
    return number_format((float)$nombre, 0, ',', ' ') . ' ' . SITE_DEVISE;
}

function slugify(string $texte): string
{
    $texte = iconv('UTF-8', 'ASCII//TRANSLIT', $texte);
    $texte = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $texte));
    return trim($texte, '-');
}

function e(string $texte): string
{
    return htmlspecialchars($texte, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------
// Produits
// ---------------------------------------------------
function getCategories(): array
{
    $pdo = getPDO();
    return $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();
}

function getProduits(array $filtres = []): array
{
    $pdo = getPDO();
    $sql = "SELECT p.*, c.nom AS categorie_nom, c.slug AS categorie_slug
            FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id
            WHERE p.actif = 1";
    $params = [];

    if (!empty($filtres['categorie'])) {
        $sql .= " AND c.slug = :cat";
        $params[':cat'] = $filtres['categorie'];
    }
    if (!empty($filtres['recherche'])) {
        $sql .= " AND (p.nom LIKE :q OR p.description LIKE :q)";
        $params[':q'] = '%' . $filtres['recherche'] . '%';
    }
    if (!empty($filtres['populaire'])) {
        $sql .= " AND p.populaire = 1";
    }

    $sql .= match ($filtres['tri'] ?? '') {
        'prix_asc'  => " ORDER BY p.prix ASC",
        'prix_desc' => " ORDER BY p.prix DESC",
        'recent'    => " ORDER BY p.created_at DESC",
        default     => " ORDER BY p.ordre ASC, p.nom ASC",
    };

    if (!empty($filtres['limite'])) {
        $sql .= " LIMIT " . (int)$filtres['limite'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProduitParSlug(string $slug): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT p.*, c.nom AS categorie_nom, c.slug AS categorie_slug
                            FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id
                            WHERE p.slug = :slug AND p.actif = 1 LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    $produit = $stmt->fetch();
    return $produit ?: null;
}

function getProduitParId(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $produit = $stmt->fetch();
    return $produit ?: null;
}

/**
 * Suggère des produits "pour accompagner" un panier : d'autres catégories
 * que celles déjà présentes, afin de proposer un complément pertinent
 * (ex : miel pour accompagner une poudre, gélules pour accompagner un jus...).
 */
function getProduitsAccompagnement(array $idsExclus, int $limite = 3): array
{
    $pdo = getPDO();
    if (empty($idsExclus)) {
        $idsExclus = [0];
    }
    $placeholders = implode(',', array_fill(0, count($idsExclus), '?'));
    $sql = "SELECT p.*, c.nom AS categorie_nom
            FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id
            WHERE p.actif = 1 AND p.stock > 0 AND p.id NOT IN ($placeholders)
            ORDER BY p.populaire DESC, RAND() LIMIT " . (int)$limite;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($idsExclus);
    return $stmt->fetchAll();
}

// ---------------------------------------------------
// Témoignages clients
// ---------------------------------------------------
function getTemoignages(array $filtres = []): array
{
    $pdo = getPDO();
    $sql = "SELECT * FROM temoignages WHERE 1=1";
    if (!empty($filtres['actif_seulement'])) {
        $sql .= " AND actif = 1";
    }
    $sql .= " ORDER BY ordre ASC, created_at DESC";
    if (!empty($filtres['limite'])) {
        $sql .= " LIMIT " . (int)$filtres['limite'];
    }
    return $pdo->query($sql)->fetchAll();
}

function getTemoignageParId(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM temoignages WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $r = $stmt->fetch();
    return $r ?: null;
}

function creerTemoignagePublic(string $nom, ?string $ville, int $note, string $texte): bool
{
    $pdo = getPDO();
    $note = max(1, min(5, $note));
    // actif = 0 : le témoignage n'apparaît sur le site qu'après validation par l'admin.
    $stmt = $pdo->prepare("INSERT INTO temoignages (nom, ville, note, texte, actif, ordre) VALUES (:nom, :ville, :note, :texte, 0, 0)");
    return $stmt->execute([
        ':nom' => $nom, ':ville' => $ville ?: null, ':note' => $note, ':texte' => $texte,
    ]);
}

// ---------------------------------------------------
// Panier (stocké en session : [produit_id => quantite])
// ---------------------------------------------------
function panierInit(): void
{
    if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
}

function panierAjouter(int $produitId, int $quantite = 1): void
{
    panierInit();
    $quantite = max(1, $quantite);
    if (isset($_SESSION['panier'][$produitId])) {
        $_SESSION['panier'][$produitId] += $quantite;
    } else {
        $_SESSION['panier'][$produitId] = $quantite;
    }
}

function panierModifier(int $produitId, int $quantite): void
{
    panierInit();
    if ($quantite <= 0) {
        unset($_SESSION['panier'][$produitId]);
    } else {
        $_SESSION['panier'][$produitId] = $quantite;
    }
}

function panierSupprimer(int $produitId): void
{
    panierInit();
    unset($_SESSION['panier'][$produitId]);
}

function panierVider(): void
{
    $_SESSION['panier'] = [];
}

function panierNombreArticles(): int
{
    panierInit();
    return array_sum($_SESSION['panier']);
}

/**
 * Retourne le détail complet du panier avec infos produit à jour
 * (prix, stock, image) directement depuis la base de données.
 */
function panierContenu(): array
{
    panierInit();
    if (empty($_SESSION['panier'])) {
        return [];
    }
    $pdo = getPDO();
    $ids = array_keys($_SESSION['panier']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produits = $stmt->fetchAll();

    $lignes = [];
    foreach ($produits as $p) {
        $qte = min($_SESSION['panier'][$p['id']], max(0, (int)$p['stock']));
        if ($qte <= 0) continue;
        $lignes[] = [
            'produit'      => $p,
            'quantite'     => $qte,
            'sous_total'   => $qte * (float)$p['prix'],
        ];
    }
    return $lignes;
}

function panierTotal(): float
{
    $total = 0;
    foreach (panierContenu() as $ligne) {
        $total += $ligne['sous_total'];
    }
    return $total;
}

function genererNumeroCommande(): string
{
    return 'SM-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
}

/**
 * Construit un lien WhatsApp pré-rempli avec le détail du panier,
 * pour que le client envoie sa commande directement au vendeur,
 * qui pourra la valider ou non.
 */
function panierLienWhatsApp(): string
{
    $lignes = panierContenu();
    $total = panierTotal();

    $texte = "Bonjour SenMoringa, je souhaite commander :\n";
    foreach ($lignes as $ligne) {
        $p = $ligne['produit'];
        $texte .= '- ' . $ligne['quantite'] . ' x ' . $p['nom'] . ' — ' . formatPrix($ligne['sous_total']) . "\n";
    }
    $texte .= "\nTotal : " . formatPrix($total) . "\n\nMerci de me confirmer la disponibilité, le mode de paiement et les modalités de livraison.";

    return 'https://wa.me/' . SITE_WHATSAPP . '?text=' . rawurlencode($texte);
}
