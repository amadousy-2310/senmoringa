<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPDO();
$nbProduits = $pdo->query("SELECT COUNT(*) FROM produits WHERE actif = 1")->fetchColumn();
$nbCommandesAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE statut = 'en_attente'")->fetchColumn();
$revenuTotal = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commandes WHERE statut != 'annulee'")->fetchColumn();
$nbStockBas = $pdo->query("SELECT COUNT(*) FROM produits WHERE stock <= 5 AND actif = 1")->fetchColumn();
$nbAvisAttente = $pdo->query("SELECT COUNT(*) FROM temoignages WHERE actif = 0")->fetchColumn();

$dernieresCommandes = $pdo->query("SELECT * FROM commandes ORDER BY created_at DESC LIMIT 6")->fetchAll();

$labelsStatut = [
    'en_attente' => ['Nouvelle', 'badge-jaune'],
    'confirmee'  => ['Confirmée', 'badge-vert'],
    'expediee'   => ['Expédiée', 'badge-vert'],
    'livree'     => ['Livrée', 'badge-vert'],
    'annulee'    => ['Annulée', 'badge-rouge'],
];

$pageAdminActive = 'dashboard';
$titreAdmin = 'Tableau de bord';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <div>
    <h1>Tableau de bord</h1>
    <p>Bienvenue, <?= e($_SESSION['admin_username']) ?>.</p>
  </div>
</div>

<div class="cartes-stats">
  <div class="carte-stat"><span class="label">Produits actifs</span><strong><?= (int)$nbProduits ?></strong></div>
  <div class="carte-stat"><span class="label">Commandes en attente</span><strong><?= (int)$nbCommandesAttente ?></strong></div>
  <div class="carte-stat"><span class="label">Chiffre d'affaires</span><strong><?= formatPrix($revenuTotal) ?></strong></div>
  <div class="carte-stat"><span class="label">Stock bas (≤5)</span><strong><?= (int)$nbStockBas ?></strong></div>
  <div class="carte-stat"><span class="label">Avis en attente</span><strong><?= (int)$nbAvisAttente ?></strong><?= $nbAvisAttente > 0 ? ' <a href="' . BASE_URL . '/admin/temoignages.php" style="font-size:0.8rem;">à modérer →</a>' : '' ?></div>
</div>

<div class="panneau">
  <div class="panneau-tete">
    <h3>Dernières commandes</h3>
    <a href="<?= BASE_URL ?>/admin/commandes.php" class="btn btn-contour btn-petit">Voir toutes les commandes</a>
  </div>
  <?php if (empty($dernieresCommandes)): ?>
    <p style="color:var(--encre-douce);">Aucune commande pour le moment.</p>
  <?php else: ?>
  <table>
    <thead><tr><th>N°</th><th>Client</th><th>Ville</th><th>Total</th><th>Statut</th><th>Date</th></tr></thead>
    <tbody>
      <?php foreach ($dernieresCommandes as $c): $label = $labelsStatut[$c['statut']] ?? ['Inconnu','badge-gris']; ?>
        <tr>
          <td><a href="<?= BASE_URL ?>/admin/commande_detail.php?id=<?= (int)$c['id'] ?>"><?= e($c['numero']) ?></a></td>
          <td><?= e($c['nom_client']) ?></td>
          <td><?= e($c['ville']) ?></td>
          <td><?= formatPrix($c['total']) ?></td>
          <td><span class="badge <?= $label[1] ?>"><?= $label[0] ?></span></td>
          <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
