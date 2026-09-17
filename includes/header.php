<?php
/**
 * $titrePage et $descriptionPage peuvent être définis avant l'include.
 * $pageActive sert à surligner le lien de nav courant.
 */
$titrePage = $titrePage ?? SITE_NOM . ' — Le moringa, naturellement.';
$descriptionPage = $descriptionPage ?? "Poudre, huile et jus de moringa, préparés artisanalement au Sénégal.";
$pageActive = $pageActive ?? '';
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($titrePage) ?></title>
<meta name="description" content="<?= e($descriptionPage) ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><rect width=%2240%22 height=%2240%22 rx=%2210%22 fill=%22%231F3A24%22/><text x=%2250%25%22 y=%2258%25%22 text-anchor=%22middle%22 font-size=%2222%22 fill=%22%23F1E9D6%22>S</text></svg>">
</head>
<body>

<div class="bandeau">
  <div class="bandeau-piste">
    <span class="bandeau-item">
      Livraison à Dakar &amp; dans tout le Sénégal — Commande rapide via
      <a href="https://wa.me/<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener">WhatsApp</a>
    </span>
    <span class="bandeau-item" aria-hidden="true">
      Livraison à Dakar &amp; dans tout le Sénégal — Commande rapide via
      <a href="https://wa.me/<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener">WhatsApp</a>
    </span>
  </div>
</div>

<header class="entete">
  <div class="conteneur">
    <a href="<?= BASE_URL ?>/index.php" class="logo logo-image">
      <img src="<?= BASE_URL ?>/assets/images/site/logo-senmoringa.png" alt="SenMoringa — The best of Senegal">
    </a>

    <nav class="nav-principale">
      <a href="<?= BASE_URL ?>/index.php" class="<?= $pageActive === 'accueil' ? 'actif' : '' ?>">Accueil</a>
      <a href="<?= BASE_URL ?>/boutique.php" class="<?= $pageActive === 'boutique' ? 'actif' : '' ?>">Boutique</a>
      <a href="<?= BASE_URL ?>/a-propos.php" class="<?= $pageActive === 'apropos' ? 'actif' : '' ?>">Notre histoire</a>
      <a href="<?= BASE_URL ?>/contact.php" class="<?= $pageActive === 'contact' ? 'actif' : '' ?>">Contact</a>
    </nav>

    <div class="entete-actions">
      <a href="<?= BASE_URL ?>/panier.php" class="btn-icone" id="lien-panier" aria-label="Voir le panier">
        <?= svgPanierIcone() ?>
        <?php $nbArticles = panierNombreArticles(); ?>
        <span class="badge-panier" id="badge-panier" style="<?= $nbArticles > 0 ? '' : 'display:none;' ?>"><?= $nbArticles ?></span>
      </a>
      <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-principal btn-petit">Commander</a>
      <button type="button" class="menu-mobile-btn" id="bouton-menu-mobile" aria-label="Ouvrir le menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
  <nav class="nav-mobile" id="nav-mobile">
    <a href="<?= BASE_URL ?>/index.php" class="<?= $pageActive === 'accueil' ? 'actif' : '' ?>">Accueil</a>
    <a href="<?= BASE_URL ?>/boutique.php" class="<?= $pageActive === 'boutique' ? 'actif' : '' ?>">Boutique</a>
    <a href="<?= BASE_URL ?>/a-propos.php" class="<?= $pageActive === 'apropos' ? 'actif' : '' ?>">Notre histoire</a>
    <a href="<?= BASE_URL ?>/contact.php" class="<?= $pageActive === 'contact' ? 'actif' : '' ?>">Contact</a>
  </nav>
</header>
