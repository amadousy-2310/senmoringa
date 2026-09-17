<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$numero = $_SESSION['derniere_commande'] ?? null;
if (!$numero) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}
unset($_SESSION['derniere_commande']);

$pageActive = '';
$titrePage = 'Commande confirmée — ' . SITE_NOM;
require __DIR__ . '/includes/header.php';
?>

<section class="page-merci">
  <div class="conteneur">
    <?= svgCoche() ?>
    <h1>Merci pour votre commande !</h1>
    <p style="color:var(--encre-douce); max-width: 50ch; margin: 14px auto 0;">Nous préparons votre colis avec soin. Notre équipe vous contactera très vite pour confirmer les détails de paiement et de livraison.</p>
    <div class="numero-commande">Commande n° <?= e($numero) ?></div>
    <div class="hero-actions" style="justify-content:center;">
      <a href="https://wa.me/<?= e(SITE_WHATSAPP) ?>?text=<?= urlencode('Bonjour, je viens de passer la commande ' . $numero . ' sur SenMoringa.') ?>" target="_blank" rel="noopener" class="btn btn-or">Confirmer sur WhatsApp</a>
      <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-contour">Continuer mes achats</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
