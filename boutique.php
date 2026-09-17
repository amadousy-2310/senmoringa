<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$categorieActive = $_GET['categorie'] ?? '';
$recherche = trim($_GET['q'] ?? '');
$tri = $_GET['tri'] ?? '';

$produits = getProduits([
    'categorie' => $categorieActive ?: null,
    'recherche' => $recherche ?: null,
    'tri' => $tri,
]);
$categories = getCategories();

$pageActive = 'boutique';
$titrePage = 'Boutique — ' . SITE_NOM;
require __DIR__ . '/includes/header.php';
?>

<div class="conteneur">
  <p class="fil-ariane"><a href="<?= BASE_URL ?>/index.php">Accueil</a> / Boutique</p>
</div>

<section class="page-boutique">
  <div class="conteneur">
    <div class="mise-en-page-boutique">

      <aside class="filtres">
        <form method="get">
          <h4>Rechercher</h4>
          <input type="text" name="q" placeholder="Un produit..." value="<?= e($recherche) ?>" style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid var(--sable-fonce);">
          <?php if ($tri): ?><input type="hidden" name="tri" value="<?= e($tri) ?>"><?php endif; ?>
        </form>

        <h4>Catégories</h4>
        <a href="<?= BASE_URL ?>/boutique.php" class="<?= $categorieActive === '' ? 'actif' : '' ?>">Toutes les catégories</a>
        <?php foreach ($categories as $cat): ?>
          <a href="<?= BASE_URL ?>/boutique.php?categorie=<?= e($cat['slug']) ?>" class="<?= $categorieActive === $cat['slug'] ? 'actif' : '' ?>"><?= e($cat['nom']) ?></a>
        <?php endforeach; ?>

        <h4>Trier par</h4>
        <form method="get" id="form-tri">
          <?php if ($categorieActive): ?><input type="hidden" name="categorie" value="<?= e($categorieActive) ?>"><?php endif; ?>
          <?php if ($recherche): ?><input type="hidden" name="q" value="<?= e($recherche) ?>"><?php endif; ?>
          <select name="tri" onchange="document.getElementById('form-tri').submit()">
            <option value="" <?= $tri === '' ? 'selected' : '' ?>>Nos préférés</option>
            <option value="recent" <?= $tri === 'recent' ? 'selected' : '' ?>>Nouveautés</option>
            <option value="prix_asc" <?= $tri === 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
            <option value="prix_desc" <?= $tri === 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
          </select>
        </form>
      </aside>

      <div>
        <div class="barre-resultats">
          <span><?= count($produits) ?> produit<?= count($produits) > 1 ? 's' : '' ?><?= $categorieActive ? ' — ' . e(array_values(array_filter($categories, fn($c) => $c['slug'] === $categorieActive))[0]['nom'] ?? '') : '' ?></span>
        </div>

        <?php include __DIR__ . '/includes/bandeau-impact.php'; ?>

        <?php if (empty($produits)): ?>
          <div class="etat-vide">
            <h3>Aucun produit trouvé</h3>
            <p>Essayez une autre catégorie ou un autre mot-clé.</p>
          </div>
        <?php else: ?>
          <div class="grille-produits">
            <?php foreach ($produits as $p): ?>
              <?php include __DIR__ . '/includes/carte-produit.php'; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
