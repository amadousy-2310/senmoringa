<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPDO();
$statutFiltre = $_GET['statut'] ?? '';

$sql = "SELECT * FROM commandes";
$params = [];
if ($statutFiltre !== '') {
    $sql .= " WHERE statut = :statut";
    $params[':statut'] = $statutFiltre;
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$commandes = $stmt->fetchAll();

$labelsStatut = [
    'en_attente' => ['Nouvelle', 'badge-jaune'],
    'confirmee'  => ['Confirmée', 'badge-vert'],
    'expediee'   => ['Expédiée', 'badge-vert'],
    'livree'     => ['Livrée', 'badge-vert'],
    'annulee'    => ['Annulée', 'badge-rouge'],
];

$pageAdminActive = 'commandes';
$titreAdmin = 'Commandes';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <div><h1>Commandes</h1><p><?= count($commandes) ?> commande(s).</p></div>
</div>

<div class="panneau">
  <div class="panneau-tete">
    <div class="actions-table">
      <a href="<?= BASE_URL ?>/admin/commandes.php" class="btn <?= $statutFiltre === '' ? 'btn-principal' : 'btn-contour' ?> btn-petit">Toutes</a>
      <?php foreach ($labelsStatut as $cle => $label): ?>
        <a href="<?= BASE_URL ?>/admin/commandes.php?statut=<?= $cle ?>" class="btn <?= $statutFiltre === $cle ? 'btn-principal' : 'btn-contour' ?> btn-petit"><?= $label[0] ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <table>
    <thead><tr><th>N°</th><th>Client</th><th>Téléphone</th><th>Ville</th><th>Total</th><th>Paiement</th><th>Statut</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($commandes as $c): $label = $labelsStatut[$c['statut']] ?? ['Inconnu','badge-gris']; ?>
        <tr>
          <td><?= e($c['numero']) ?></td>
          <td><?= e($c['nom_client']) ?></td>
          <td><?= e($c['telephone']) ?></td>
          <td><?= e($c['ville']) ?></td>
          <td><?= formatPrix($c['total']) ?></td>
          <td><?= e(str_replace('_', ' ', ucfirst($c['mode_paiement']))) ?></td>
          <td><span class="badge <?= $label[1] ?>"><?= $label[0] ?></span></td>
          <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
          <td><a href="<?= BASE_URL ?>/admin/commande_detail.php?id=<?= (int)$c['id'] ?>" class="btn btn-contour btn-petit">Détail</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($commandes)): ?><tr><td colspan="9">Aucune commande.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
