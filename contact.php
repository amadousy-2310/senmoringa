<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$envoye = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ici, vous pouvez brancher un envoi d'e-mail (mail() ou PHPMailer) si votre hébergeur le permet.
    $envoye = true;
}

$pageActive = 'contact';
$titrePage = 'Contact — ' . SITE_NOM;
require __DIR__ . '/includes/header.php';
?>

<div class="conteneur">
  <p class="fil-ariane"><a href="<?= BASE_URL ?>/index.php">Accueil</a> / Contact</p>
</div>

<section class="page-simple">
  <div class="conteneur">
    <div class="section-tete" style="margin-bottom: 10px;">
      <span class="eyebrow">Une question ?</span>
      <h1 style="margin-top:14px;">Parlons-en</h1>
    </div>

    <div class="grille-contact" style="margin-top:40px;">
      <div>
        <?php if ($envoye): ?>
          <div class="alerte alerte-succes">Votre message a bien été envoyé. Nous vous répondrons rapidement.</div>
        <?php endif; ?>
        <form method="post">
          <div class="groupe-champ">
            <label for="nom">Nom complet</label>
            <input type="text" id="nom" name="nom" required>
          </div>
          <div class="groupe-champ">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="groupe-champ">
            <label for="sujet">Sujet</label>
            <input type="text" id="sujet" name="sujet">
          </div>
          <div class="groupe-champ">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" required></textarea>
          </div>
          <button type="submit" class="btn btn-principal">Envoyer le message</button>
        </form>
      </div>

      <div>
        <div class="carte-contact-info">
          <div class="icone"><?= svgIconeBienfait('energie') ?></div>
          <div><strong>Téléphone</strong><p><?= e(SITE_TELEPHONE) ?></p></div>
        </div>
        <div class="carte-contact-info">
          <div class="icone"><?= svgIconeBienfait('nutrition') ?></div>
          <div><strong>Email</strong><p><?= e(SITE_EMAIL) ?></p></div>
        </div>
        <div class="carte-contact-info">
          <div class="icone"><?= svgIconeBienfait('immunite') ?></div>
          <div><strong>Adresse</strong><p><?= e(SITE_ADRESSE) ?></p></div>
        </div>
        <a href="https://wa.me/<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn btn-or" style="margin-top:26px;">Nous écrire sur WhatsApp</a>
        <div class="reseaux-sociaux" style="margin-top:22px;">
          <a href="<?= e(SITE_FACEBOOK) ?>" target="_blank" rel="noopener" aria-label="Facebook" style="background:var(--sable-fonce); color:var(--vert-foret);"><?= svgIconeReseau('facebook') ?></a>
          <a href="<?= e(SITE_INSTAGRAM) ?>" target="_blank" rel="noopener" aria-label="Instagram" style="background:var(--sable-fonce); color:var(--vert-foret);"><?= svgIconeReseau('instagram') ?></a>
          <a href="<?= e(SITE_LINKEDIN) ?>" target="_blank" rel="noopener" aria-label="LinkedIn" style="background:var(--sable-fonce); color:var(--vert-foret);"><?= svgIconeReseau('linkedin') ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
