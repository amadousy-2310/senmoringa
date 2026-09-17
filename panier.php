<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$lignes = panierContenu();
$total = panierTotal();

$idsDansPanier = array_map(fn($l) => $l['produit']['id'], $lignes);
$suggestions = getProduitsAccompagnement($idsDansPanier, 3);

$pageActive = '';
$titrePage = 'Mon panier — ' . SITE_NOM;
require __DIR__ . '/includes/header.php';
?>

<div class="conteneur">
  <p class="fil-ariane"><a href="<?= BASE_URL ?>/index.php">Accueil</a> / Mon panier</p>
</div>

<section class="page-panier">
  <div class="conteneur">

    <?php if (!empty($_SESSION['message_panier'])): ?>
      <div class="alerte alerte-succes"><?= $_SESSION['message_panier'] ?></div>
      <?php unset($_SESSION['message_panier']); ?>
    <?php endif; ?>

    <?php if (empty($lignes)): ?>
      <div class="panier-vide">
        <?= svgPanierVideIllustration() ?>
        <h2>Votre panier est vide</h2>
        <p style="color:var(--encre-douce); margin: 12px 0 26px;">Parcourez la boutique pour découvrir nos poudres, huiles et jus de moringa.</p>
        <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-principal">Découvrir la boutique</a>
      </div>
    <?php else: ?>

      <h1 style="margin-bottom: 30px;">Mon panier</h1>

      <div class="mise-en-page-panier">
        <form method="post" action="<?= BASE_URL ?>/panier_maj.php">
          <table class="tableau-panier">
            <thead>
              <tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Sous-total</th><th></th></tr>
            </thead>
            <tbody>
              <?php foreach ($lignes as $ligne): $p = $ligne['produit']; ?>
                <tr>
                  <td>
                    <div class="ligne-produit-panier">
                      <div class="miniature-panier">
                        <?php if (!empty($p['image'])): ?>
                          <img src="<?= BASE_URL ?>/assets/images/produits/<?= e($p['image']) ?>" alt="<?= e($p['nom']) ?>">
                        <?php else: ?>
                          <?= svgVignetteProduit(null) ?>
                        <?php endif; ?>
                      </div>
                      <div>
                        <h4><a href="<?= BASE_URL ?>/produit.php?slug=<?= e($p['slug']) ?>"><?= e($p['nom']) ?></a></h4>
                        <a href="<?= BASE_URL ?>/panier_supprimer.php?id=<?= (int)$p['id'] ?>" class="lien-supprimer">Retirer</a>
                      </div>
                    </div>
                  </td>
                  <td class="prix"><?= formatPrix($p['prix']) ?></td>
                  <td>
                    <input type="number" name="quantites[<?= (int)$p['id'] ?>]" value="<?= (int)$ligne['quantite'] ?>" min="0" max="<?= (int)$p['stock'] ?>"
                           style="width:64px; padding:8px; border-radius:8px; border:1px solid var(--sable-fonce);">
                  </td>
                  <td class="prix"><?= formatPrix($ligne['sous_total']) ?></td>
                  <td></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <button type="submit" class="btn btn-contour btn-petit" style="margin-top:20px;">Mettre à jour le panier</button>
        </form>

        <aside class="recap-panier">
          <h3>Récapitulatif</h3>
          <div class="ligne-recap"><span>Sous-total</span><span><?= formatPrix($total) ?></span></div>
          <div class="ligne-recap"><span>Livraison</span><span>Calculée à l'étape suivante</span></div>
          <div class="ligne-recap total"><span>Total</span><span><?= formatPrix($total) ?></span></div>
          <a href="<?= e(panierLienWhatsApp()) ?>" target="_blank" rel="noopener" class="btn btn-principal btn-bloc" style="margin-top:20px;">Commander sur WhatsApp</a>
          <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-texte" style="display:block; text-align:center; margin-top:16px;">← Continuer mes achats</a>
        </aside>
      </div>

      <?php if (!empty($suggestions)): ?>
        <div class="bloc-accompagnement-panier">
          <span class="eyebrow">Pour accompagner votre commande</span>
          <h3 style="margin: 10px 0 24px;">Nos suggestions</h3>
          <div class="grille-produits">
            <?php foreach ($suggestions as $p): ?>
              <?php include __DIR__ . '/includes/carte-produit.php'; ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
