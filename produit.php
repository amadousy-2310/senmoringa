<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$slug = $_GET['slug'] ?? '';
$produit = $slug ? getProduitParSlug($slug) : null;

if (!$produit) {
    http_response_code(404);
    $pageActive = '';
    $titrePage = 'Produit introuvable — ' . SITE_NOM;
    require __DIR__ . '/includes/header.php';
    echo '<div class="conteneur"><div class="etat-vide"><h3>Ce produit n\'existe pas ou n\'est plus disponible.</h3><p><a class="btn btn-principal" style="margin-top:16px;" href="' . BASE_URL . '/boutique.php">Retour à la boutique</a></p></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$suggestions = getProduitsAccompagnement([$produit['id']], 3);

$enRupture = $produit['stock'] <= 0;
$stockBas = $produit['stock'] > 0 && $produit['stock'] <= 5;

$pageActive = 'boutique';
$titrePage = $produit['nom'] . ' — ' . SITE_NOM;
$descriptionPage = $produit['description_courte'] ?? '';
require __DIR__ . '/includes/header.php';
?>

<div class="conteneur">
  <p class="fil-ariane">
    <a href="<?= BASE_URL ?>/index.php">Accueil</a> /
    <a href="<?= BASE_URL ?>/boutique.php">Boutique</a> /
    <?php if (!empty($produit['categorie_slug'])): ?>
      <a href="<?= BASE_URL ?>/boutique.php?categorie=<?= e($produit['categorie_slug']) ?>"><?= e($produit['categorie_nom']) ?></a> /
    <?php endif; ?>
    <?= e($produit['nom']) ?>
  </p>
</div>

<section class="page-produit">
  <div class="conteneur">
    <?php include __DIR__ . '/includes/bandeau-impact.php'; ?>
    <div class="grille-produit-detail">
      <div class="visuel-produit-detail">
        <?php if (!empty($produit['image'])): ?>
          <img src="<?= BASE_URL ?>/assets/images/produits/<?= e($produit['image']) ?>" alt="<?= e($produit['nom']) ?>">
        <?php else: ?>
          <?= svgVignetteProduit($produit['categorie_slug'] ?? null) ?>
        <?php endif; ?>
      </div>

      <div class="infos-produit-detail">
        <span class="cat-nom"><?= e($produit['categorie_nom'] ?? '') ?></span>
        <h1><?= e($produit['nom']) ?></h1>
        <div class="ligne-prix">
          <span class="prix"><?= formatPrix($produit['prix']) ?></span>
          <?php if (!empty($produit['ancien_prix'])): ?><span class="prix-barre"><?= formatPrix($produit['ancien_prix']) ?></span><?php endif; ?>
        </div>
        <p class="description"><?= nl2br(e($produit['description'] ?? '')) ?></p>

        <div class="stock-info">
          <?php if ($enRupture): ?>
            <span class="point-stock rupture"></span> Rupture de stock
          <?php elseif ($stockBas): ?>
            <span class="point-stock bas"></span> Il ne reste que <?= (int)$produit['stock'] ?> unité<?= $produit['stock'] > 1 ? 's' : '' ?>
          <?php else: ?>
            <span class="point-stock"></span> En stock
          <?php endif; ?>
        </div>

        <?php if (!$enRupture): ?>
          <form method="post" action="<?= BASE_URL ?>/panier_ajouter.php" class="form-ajout-panier">
            <input type="hidden" name="produit_id" value="<?= (int)$produit['id'] ?>">
            <input type="hidden" name="retour" value="<?= e($_SERVER['REQUEST_URI']) ?>">
            <div class="selecteur-qte">
              <button type="button" onclick="ajusterQte(-1)" aria-label="Diminuer">−</button>
              <input type="number" name="quantite" id="champ-qte" value="1" min="1" max="<?= (int)$produit['stock'] ?>" inputmode="numeric">
              <button type="button" onclick="ajusterQte(1)" aria-label="Augmenter">+</button>
            </div>
            <button type="submit" class="btn btn-principal">Ajouter au panier</button>
          </form>
        <?php else: ?>
          <a href="https://wa.me/<?= e(SITE_WHATSAPP) ?>?text=<?= urlencode('Bonjour, quand sera de nouveau disponible : ' . $produit['nom'] . ' ?') ?>" target="_blank" rel="noopener" class="btn btn-contour">Être prévenu(e) sur WhatsApp</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($suggestions)): ?>
      <div class="accompagnement">
        <span class="eyebrow">Ça se marie bien avec</span>
        <h3>Complétez votre commande</h3>
        <div class="grille-produits">
          <?php foreach ($suggestions as $p): ?>
            <?php include __DIR__ . '/includes/carte-produit.php'; ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
function ajusterQte(delta) {
  const champ = document.getElementById('champ-qte');
  let val = parseInt(champ.value || '1', 10) + delta;
  const max = parseInt(champ.max || '99', 10);
  if (val < 1) val = 1;
  if (val > max) val = max;
  champ.value = val;
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
