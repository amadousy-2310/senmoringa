<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$envoye = false;
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Piège à robots : ce champ doit toujours rester vide pour un vrai visiteur.
    if (!empty($_POST['site_web'])) {
        $envoye = true; // on fait semblant que ça a marché, sans rien enregistrer
    } else {
        $nom = trim($_POST['nom'] ?? '');
        $ville = trim($_POST['ville'] ?? '');
        $note = (int)($_POST['note'] ?? 0);
        $texte = trim($_POST['texte'] ?? '');

        if ($nom === '') $erreurs[] = "Merci d'indiquer votre nom.";
        if ($texte === '' || mb_strlen($texte) < 10) $erreurs[] = "Votre témoignage doit contenir au moins quelques mots.";
        if ($note < 1 || $note > 5) $erreurs[] = "Merci de choisir une note entre 1 et 5 étoiles.";

        if (empty($erreurs)) {
            creerTemoignagePublic($nom, $ville, $note, $texte);
            $envoye = true;
        }
    }
}

$pageActive = 'avis';
$titrePage = 'Laisser un avis — ' . SITE_NOM;
require __DIR__ . '/includes/header.php';
?>

<div class="conteneur">
  <p class="fil-ariane"><a href="<?= BASE_URL ?>/index.php">Accueil</a> / Laisser un avis</p>
</div>

<section class="page-simple">
  <div class="conteneur" style="max-width:640px;">
    <div class="section-tete" style="margin-bottom: 10px;">
      <span class="eyebrow">Votre expérience compte</span>
      <h1 style="margin-top:14px;">Partagez votre avis</h1>
      <p style="margin-top:14px; color:var(--encre-douce);">Vous avez testé nos produits ? Racontez-nous ce que vous en pensez — votre témoignage pourra être publié sur la page d'accueil après relecture.</p>
    </div>

    <?php if ($envoye): ?>
      <div class="alerte alerte-succes">Merci beaucoup pour votre témoignage ! Il sera publié sur le site après validation.</div>
    <?php endif; ?>

    <?php if (!empty($erreurs)): ?>
      <div class="alerte alerte-erreur"><?php foreach ($erreurs as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
    <?php endif; ?>

    <?php if (!$envoye): ?>
    <form method="post" class="panneau-avis">
      <!-- Champ piège anti-robot, invisible pour un humain -->
      <div style="position:absolute; left:-9999px;" aria-hidden="true">
        <label for="site_web">Ne pas remplir ce champ</label>
        <input type="text" id="site_web" name="site_web" tabindex="-1" autocomplete="off">
      </div>

      <div class="ligne-2">
        <div class="groupe-champ">
          <label for="nom">Votre nom</label>
          <input type="text" id="nom" name="nom" value="<?= e($_POST['nom'] ?? '') ?>" required>
        </div>
        <div class="groupe-champ">
          <label for="ville">Votre ville (optionnel)</label>
          <input type="text" id="ville" name="ville" value="<?= e($_POST['ville'] ?? '') ?>">
        </div>
      </div>

      <div class="groupe-champ">
        <label>Votre note</label>
        <div class="choix-etoiles">
          <?php for ($n = 5; $n >= 1; $n--): ?>
            <input type="radio" id="note<?= $n ?>" name="note" value="<?= $n ?>" <?= ((int)($_POST['note'] ?? 5) === $n) ? 'checked' : '' ?>>
            <label for="note<?= $n ?>" title="<?= $n ?>/5">★</label>
          <?php endfor; ?>
        </div>
      </div>

      <div class="groupe-champ">
        <label for="texte">Votre témoignage</label>
        <textarea id="texte" name="texte" rows="5" required placeholder="Qu'avez-vous pensé de nos produits ?"><?= e($_POST['texte'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn btn-principal">Envoyer mon avis</button>
    </form>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
