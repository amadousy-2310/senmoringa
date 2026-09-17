<?php
/** Attend une variable $p (ligne produit) dans le scope appelant. */
$stockBas = $p['stock'] > 0 && $p['stock'] <= 5;
$enRupture = $p['stock'] <= 0;
?>
<div class="carte-produit">
  <a href="<?= BASE_URL ?>/produit.php?slug=<?= e($p['slug']) ?>" class="vignette">
    <?php if (!empty($p['image'])): ?>
      <img src="<?= BASE_URL ?>/assets/images/produits/<?= e($p['image']) ?>" alt="<?= e($p['nom']) ?>" loading="lazy">
    <?php else: ?>
      <?= svgVignetteProduit($p['categorie_slug'] ?? null) ?>
    <?php endif; ?>
    <?php if ($enRupture): ?>
      <span class="etiquette stock-bas">Rupture</span>
    <?php elseif ($stockBas): ?>
      <span class="etiquette stock-bas">Stock bas</span>
    <?php elseif (!empty($p['populaire'])): ?>
      <span class="etiquette">Populaire</span>
    <?php endif; ?>
  </a>
  <div class="infos">
    <span class="cat-nom"><?= e($p['categorie_nom'] ?? '') ?></span>
    <h3><a href="<?= BASE_URL ?>/produit.php?slug=<?= e($p['slug']) ?>"><?= e($p['nom']) ?></a></h3>
    <p class="desc"><?= e($p['description_courte'] ?? '') ?></p>
    <div class="ligne-prix">
      <span class="prix"><?= formatPrix($p['prix']) ?></span>
      <?php if (!empty($p['ancien_prix'])): ?><span class="prix-barre"><?= formatPrix($p['ancien_prix']) ?></span><?php endif; ?>
    </div>
    <form method="post" action="<?= BASE_URL ?>/panier_ajouter.php">
      <input type="hidden" name="produit_id" value="<?= (int)$p['id'] ?>">
      <input type="hidden" name="quantite" value="1">
      <input type="hidden" name="retour" value="<?= e($_SERVER['REQUEST_URI']) ?>">
      <button type="submit" class="btn btn-principal btn-bloc btn-petit" <?= $enRupture ? 'disabled' : '' ?>>
        <?= $enRupture ? 'Indisponible' : 'Ajouter au panier' ?>
      </button>
    </form>
  </div>
</div>
