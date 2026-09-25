<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$categories = getCategories();
$ordreGammes = ['poudre' => 0, 'jus' => 1, 'huile' => 2];
usort($categories, fn($a, $b) => ($ordreGammes[$a['slug']] ?? 99) <=> ($ordreGammes[$b['slug']] ?? 99));
$populaires = getProduits(['populaire' => true, 'limite' => 4]);
if (count($populaires) < 4) {
    $populaires = getProduits(['limite' => 4]);
}
$temoignages = getTemoignages(['actif_seulement' => true, 'limite' => 6]);

$pageActive = 'accueil';
$titrePage = 'SenMoringa — Poudre, huile et jus de moringa 100% naturels';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="conteneur">
    <div class="hero-texte">
      <span class="eyebrow">L'arbre miracle, cultivé au Sénégal</span>
      <h1>Le moringa, tel que la nature l'a pensé.</h1>
      <p class="intro">Poudre, huile et jus préparés à partir de feuilles et de graines récoltées à maturité, transformées dans les règles de l'art — sans additif, sans raccourci.</p>
      <div class="hero-actions">
        <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-principal">Découvrir la boutique</a>
        <a href="<?= BASE_URL ?>/a-propos.php" class="btn btn-contour">Notre histoire</a>
      </div>
      <div class="hero-stats">
        <div><strong data-compte-jusqua="100" data-suffixe="%">0%</strong><span>Naturel, sans additif</span></div>
        <div><strong data-compte-jusqua="500" data-suffixe="+">0+</strong><span>Familles servies</span></div>
        <div><strong data-compte-jusqua="48" data-suffixe="h">0h</strong><span>Livraison à Dakar</span></div>
      </div>
    </div>
    <div class="hero-visuel">
      <div class="cadre-feuille"><img src="<?= BASE_URL ?>/assets/images/site/feuille-moringa.png" alt="Feuille de moringa" class="photo-feuille"></div>
      <div class="pastille-flottante pastille-1"><?= svgIconeBienfait('nutrition') ?><span><strong>92</strong>nutriments essentiels</span></div>
      <div class="pastille-flottante pastille-2"><?= svgIconeBienfait('immunite') ?><span><strong>0</strong>conservateur ajouté</span></div>
    </div>
  </div>
</section>

<?= svgVigneDiviseur() ?>

<section class="section" style="padding-bottom: 0;">
  <div class="conteneur">
    <div class="section-tete">
      <span class="eyebrow">Au quotidien</span>
      <h2>Sur le terrain, avec notre équipe</h2>
      <p>De la plantation à l'atelier, en passant par la formation de notre équipe — un aperçu de ce qui se passe derrière chaque flacon.</p>
    </div>
  </div>
  <div class="galerie-defilement galerie-defilement-grande">
    <div class="galerie-defilement-piste">
      <?php
        $photosGalerie = [
          'equipe-accueil-jeufzone.jpg' => "Notre équipe à l'entrée de la ferme JeufZone",
          'atelier-transformation.jpg'  => 'Préparation des ingrédients en atelier',
          'remise-certificat-1.jpg'     => "Remise de certificat de formation",
          'champ-plantation.jpg'        => 'Suivi de la plantation sur le terrain',
          'equipe-ferme.jpg'            => "L'équipe SenMoringa sur le site de production",
          'remise-certificat-2.jpg'     => 'Formation Jeufzone Farm — Leadership agricole',
          'polo-marque.jpg'             => 'La tenue SenMoringa',
        ];
        // On affiche la liste deux fois de suite pour créer une boucle de défilement continue
        for ($tour = 0; $tour < 2; $tour++):
          foreach ($photosGalerie as $fichier => $legende):
      ?>
        <img src="<?= BASE_URL ?>/assets/images/galerie/<?= e($fichier) ?>" alt="<?= e($legende) ?>" loading="lazy">
      <?php
          endforeach;
        endfor;
      ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="conteneur">
    <div class="section-tete">
      <span class="eyebrow">Pourquoi le moringa</span>
      <h2>Un allié pour le quotidien</h2>
      <p>Surnommé « l'arbre miracle », le moringa accompagne l'énergie, l'immunité et la beauté de la peau et des cheveux — au naturel.</p>
    </div>
    <div class="grille-bienfaits">
      <div class="carte-bienfait">
        <div class="icone"><?= svgIconeBienfait('nutrition') ?></div>
        <h3>Riche en nutriments</h3>
        <p>Vitamines, fer, calcium et acides aminés concentrés dans chaque feuille séchée.</p>
      </div>
      <div class="carte-bienfait">
        <div class="icone"><?= svgIconeBienfait('energie') ?></div>
        <h3>Énergie naturelle</h3>
        <p>Un coup de pouce pour les journées chargées, sans excitant artificiel.</p>
      </div>
      <div class="carte-bienfait">
        <div class="icone"><?= svgIconeBienfait('peau') ?></div>
        <h3>Peau &amp; cheveux</h3>
        <p>L'huile vierge nourrit et répare en profondeur, du visage aux pointes.</p>
      </div>
      <div class="carte-bienfait">
        <div class="icone"><?= svgIconeBienfait('immunite') ?></div>
        <h3>Soutien immunitaire</h3>
        <p>Des antioxydants qui accompagnent les défenses naturelles de l'organisme.</p>
      </div>
    </div>

    <div class="grille-produit-detail" style="margin-top:60px; align-items:center;">
      <div class="visuel-bienfaits">
        <img src="<?= BASE_URL ?>/assets/images/site/bienfaits-moringa-feuille.png" alt="Les bienfaits du moringa (Nébéday)" loading="lazy">
      </div>
      <div>
        <span class="eyebrow">L'arbre miracle</span>
        <h2 style="margin-top:14px;">Les bienfaits du moringa, ou Nébéday</h2>
        <ul style="margin-top:18px; display:grid; gap:12px; color:var(--encre-douce);">
          <li>✔ Détoxifie le corps</li>
          <li>✔ Stimule le métabolisme</li>
          <li>✔ Améliore la digestion</li>
          <li>✔ Réduit le stress oxydatif</li>
          <li>✔ Favorise la concentration</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section section-sable">
  <div class="conteneur">
    <div class="section-tete">
      <span class="eyebrow">Nos gammes</span>
      <h2>Une forme pour chaque usage</h2>
    </div>
    <div class="grille-categories">
      <?php
      $photosGammes = [
          'poudre' => 'poudre-moringa-50g.jpg',
          'jus'    => 'nebedaye-250ml.jpg',
          'huile'  => 'huile-pousse-cheveux-moringa-30ml.jpg',
      ];
      ?>
      <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>/boutique.php?categorie=<?= e($cat['slug']) ?>" class="carte-categorie">
          <?php if (!empty($photosGammes[$cat['slug']])): ?>
            <img class="photo-categorie" src="<?= BASE_URL ?>/assets/images/produits/<?= e($photosGammes[$cat['slug']]) ?>" alt="<?= e($cat['nom']) ?>">
            <div class="voile-categorie"></div>
          <?php endif; ?>
          <?= svgIconeCategorie($cat['slug']) ?>
          <span>Gamme</span>
          <h3><?= e($cat['nom']) ?></h3>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="conteneur">
    <div class="section-tete">
      <span class="eyebrow">Les préférés</span>
      <h2>Nos produits populaires</h2>
    </div>
    <?php include __DIR__ . '/includes/bandeau-impact.php'; ?>
    <div class="grille-produits">
      <?php foreach ($populaires as $p): ?>
        <?php include __DIR__ . '/includes/carte-produit.php'; ?>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center; margin-top:44px;">
      <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-contour">Voir toute la boutique</a>
    </div>
  </div>
</section>

<?= svgVigneDiviseur() ?>

<!--
  SECTION "VIVANTE" — en attendant une vraie vidéo.
  Pour la remplacer par une vraie vidéo plus tard :
    1) Dépose ton fichier .mp4 dans assets/videos/ (ex: assets/videos/moringa.mp4)
    2) Remplace le <div class="fond-anime-fallback"></div> ci-dessous par :
       <video class="fond-video" autoplay muted loop playsinline>
         <source src="<?= BASE_URL ?>/assets/videos/moringa.mp4" type="video/mp4">
       </video>
    3) C'est tout — le voile sombre et le texte restent identiques.
-->
<section class="section-video-vivante">
  <div class="fond-anime">
    <div class="fond-anime-fallback"></div>
    <img class="feuille-derive feuille-derive-1" src="<?= BASE_URL ?>/assets/images/site/feuille-moringa.png" alt="">
    <img class="feuille-derive feuille-derive-2" src="<?= BASE_URL ?>/assets/images/site/feuille-moringa.png" alt="">
    <img class="feuille-derive feuille-derive-3" src="<?= BASE_URL ?>/assets/images/site/feuille-moringa.png" alt="">
  </div>
  <div class="voile-video"></div>
  <div class="conteneur contenu-video-vivante">
    <span class="eyebrow" style="color:var(--or);">L'esprit Nébéday</span>
    <h2>Du champ au flacon, la vie du moringa ne s'arrête jamais</h2>
    <p>Récolté à la main, séché à l'ombre, transformé avec soin — chaque geste compte pour préserver ce que la nature a de meilleur.</p>
  </div>
</section>

<section class="section">
  <div class="conteneur">
    <div class="section-tete">
      <span class="eyebrow">Ils nous font confiance</span>
      <h2>Ce qu'en disent nos client(e)s</h2>
    </div>
    <div class="grille-temoignages">
      <?php foreach ($temoignages as $t): ?>
        <?php include __DIR__ . '/includes/carte-temoignage.php'; ?>
      <?php endforeach; ?>
      <?php if (empty($temoignages)): ?>
        <p class="aide">Aucun témoignage pour le moment.</p>
      <?php endif; ?>
    </div>
    <div style="text-align:center; margin-top:36px;">
      <a href="<?= BASE_URL ?>/avis.php" class="btn btn-contour">✍️ Laisser mon propre avis</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="conteneur">
    <div class="cta">
      <span class="eyebrow" style="color:var(--or);">Une question avant de commander ?</span>
      <h2>Discutons de votre commande sur WhatsApp</h2>
      <p>Notre équipe répond directement à vos questions sur les produits, les quantités et la livraison.</p>
      <div class="cta-actions">
        <a href="https://wa.me/<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn btn-or">Écrire sur WhatsApp</a>
        <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-contour" style="border-color:var(--sable); color:var(--sable);">Voir la boutique</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
