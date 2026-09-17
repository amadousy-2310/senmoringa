<footer class="pied">
  <div class="conteneur">
    <div class="grille-pied">
      <div>
        <a href="<?= BASE_URL ?>/index.php" class="logo logo-image logo-pied">
          <img src="<?= BASE_URL ?>/assets/images/site/logo-senmoringa-pied.png" alt="SenMoringa — The best of Senegal">
        </a>
        <p>Poudre, huile et jus de moringa préparés artisanalement, de la récolte à votre porte. L'arbre miracle, cultivé et transformé au Sénégal.</p>
        <div class="reseaux-sociaux">
          <a href="<?= e(SITE_FACEBOOK) ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= svgIconeReseau('facebook') ?></a>
          <a href="<?= e(SITE_INSTAGRAM) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= svgIconeReseau('instagram') ?></a>
          <a href="<?= e(SITE_LINKEDIN) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><?= svgIconeReseau('linkedin') ?></a>
        </div>
      </div>
      <div>
        <h4>Boutique</h4>
        <ul>
          <li><a href="<?= BASE_URL ?>/boutique.php?categorie=poudre">Poudre</a></li>
          <li><a href="<?= BASE_URL ?>/boutique.php?categorie=huile">Huile</a></li>
          <li><a href="<?= BASE_URL ?>/boutique.php?categorie=jus">Jus</a></li>
        </ul>
      </div>
      <div>
        <h4>SenMoringa</h4>
        <ul>
          <li><a href="<?= BASE_URL ?>/a-propos.php">Notre histoire</a></li>
          <li><a href="<?= BASE_URL ?>/contact.php">Contact</a></li>
          <li><a href="<?= BASE_URL ?>/avis.php">Laisser un avis</a></li>
          <li><a href="<?= BASE_URL ?>/panier.php">Mon panier</a></li>
          <li><a href="<?= BASE_URL ?>/admin/login.php">Espace pro</a></li>
        </ul>
      </div>
      <div>
        <h4>Nous contacter</h4>
        <ul>
          <li><?= e(SITE_ADRESSE) ?></li>
          <li><?= e(SITE_TELEPHONE) ?></li>
          <li><?= e(SITE_EMAIL) ?></li>
        </ul>
      </div>
    </div>
    <div class="pied-bas">
      <span>© <?= date('Y') ?> SenMoringa. Tous droits réservés.</span>
      <span>Fait avec soin à Dakar, Sénégal.</span>
    </div>
  </div>
</footer>

<button type="button" id="bouton-haut" aria-label="Remonter en haut de page">↑</button>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
