<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$pageActive = 'apropos';
$titrePage = 'Notre histoire — ' . SITE_NOM;
require __DIR__ . '/includes/header.php';
?>

<div class="conteneur">
  <p class="fil-ariane"><a href="<?= BASE_URL ?>/index.php">Accueil</a> / Notre histoire</p>
</div>

<section class="page-simple">
  <div class="conteneur">
    <div class="section-tete" style="margin-bottom: 30px;">
      <span class="eyebrow">Depuis la terre sénégalaise</span>
      <h1 style="margin-top:14px;">Une histoire de patience et de racines</h1>
      <p>SenMoringa est né d'une conviction simple : les bienfaits du moringa méritent d'être transformés avec le même soin que celui apporté à sa culture.</p>
    </div>

    <div class="grille-produit-detail" style="align-items:center;">
      <div>
        <p style="margin-bottom:18px; color:var(--encre-douce);">Tout a commencé avec un moringa planté en bordure de champ, presque par curiosité. Ses feuilles résistaient à la sécheresse quand d'autres cultures peinaient. De cette observation est née une petite production familiale, puis un atelier de transformation, puis SenMoringa.</p>
        <p style="margin-bottom:18px; color:var(--encre-douce);">Aujourd'hui, nous travaillons avec un réseau de producteurs situés autour de Thiès. Chaque feuille est séchée à l'ombre, chaque graine pressée à froid, pour que rien ne se perde de ce que la plante a à offrir.</p>
        <p style="color:var(--encre-douce);">Notre objectif reste inchangé : proposer des produits honnêtes, sans additif inutile, à un prix qui respecte autant le client que le producteur.</p>
      </div>
      <div class="visuel-produit-detail visuel-bienfaits">
        <img src="<?= BASE_URL ?>/assets/images/site/histoire-collage.png" alt="SenMoringa — du champ à la transformation artisanale">
      </div>
    </div>

    <div class="grille-produit-detail" style="align-items:stretch; margin-top:50px;">
      <div class="carte-bienfait" style="padding:36px;">
        <div class="icone"><?= svgIconeBienfait('energie') ?></div>
        <h3>Mission</h3>
        <p>Fournir un moringa 100&nbsp;% naturel et de qualité, cultivé et transformé localement, pour promouvoir la santé, l'autosuffisance alimentaire, l'emploi des jeunes et des femmes, ainsi que le développement durable des communautés rurales.</p>
      </div>
      <div class="carte-bienfait" style="padding:36px;">
        <div class="icone"><?= svgIconeBienfait('immunite') ?></div>
        <h3>Vision</h3>
        <p>Devenir la référence du moringa au Sénégal et en Afrique, reconnue pour sa qualité, son éthique, sa durabilité, et faire connaître ses bienfaits sur les marchés locaux et internationaux.</p>
      </div>
    </div>

    <div class="grille-valeurs">
      <div class="carte-bienfait">
        <div class="icone"><?= svgIconeBienfait('nutrition') ?></div>
        <h3>Sans compromis</h3>
        <p>Aucun additif, aucun raccourci de production. Ce que vous lisez sur l'étiquette, c'est ce qu'il y a dans le flacon.</p>
      </div>
      <div class="carte-bienfait">
        <div class="icone"><?= svgIconeBienfait('energie') ?></div>
        <h3>Circuit court</h3>
        <p>Nos producteurs sont rémunérés justement, et la transformation se fait localement, à Thiès.</p>
      </div>
      <div class="carte-bienfait">
        <div class="icone"><?= svgIconeBienfait('immunite') ?></div>
        <h3>Exigence constante</h3>
        <p>Chaque lot est contrôlé avant expédition, pour une qualité qui ne varie pas d'une commande à l'autre.</p>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
