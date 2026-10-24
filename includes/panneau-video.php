<?php
/**
 * Panneau vidéo d'utilisation d'un produit.
 * Attend : $pv (ligne produit) et $videoProduit (voir getVideoProduit()).
 * Options : $masquerLienFiche = true pour cacher le lien "Voir la fiche produit" ;
 *           $panneauOuvert = true pour afficher la vidéo directement (fiche produit).
 */
?>
<section class="panneau-video" id="video-<?= e($pv['slug']) ?>"<?= empty($panneauOuvert) ? ' hidden' : '' ?> aria-label="<?= e($videoProduit['titre']) ?>">
  <?php if (empty($panneauOuvert)): ?>
    <button type="button" class="panneau-video-fermer" aria-label="Fermer la vidéo">×</button>
  <?php endif; ?>
  <div class="panneau-video-lecteur">
    <video controls playsinline controlsList="nofullscreen nodownload" disablePictureInPicture preload="<?= empty($panneauOuvert) ? 'none' : 'metadata' ?>"
           poster="<?= BASE_URL ?>/assets/videos/<?= e($videoProduit['poster']) ?>">
      <source src="<?= BASE_URL ?>/assets/videos/<?= e($videoProduit['fichier']) ?>" type="video/mp4">
      Votre navigateur ne peut pas lire cette vidéo.
    </video>
  </div>
  <div class="panneau-video-texte">
    <span class="cat-nom">Mode d'emploi</span>
    <h3><?= e($videoProduit['titre']) ?></h3>
    <p class="panneau-video-produit"><?= e($pv['nom']) ?> · <?= formatPrix($pv['prix']) ?></p>
    <ol>
      <?php foreach ($videoProduit['etapes'] as $etape): ?><li><?= e($etape) ?></li><?php endforeach; ?>
    </ol>
    <?php if (!empty($videoProduit['astuce'])): ?><p class="panneau-video-astuce"><?= e($videoProduit['astuce']) ?></p><?php endif; ?>
    <?php if (empty($masquerLienFiche)): ?>
      <a class="btn btn-contour btn-petit" href="<?= BASE_URL ?>/produit.php?slug=<?= e($pv['slug']) ?>">Voir la fiche produit</a>
    <?php endif; ?>
  </div>
</section>
