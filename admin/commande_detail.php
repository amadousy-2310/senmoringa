<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut'])) {
    $pdo->prepare("UPDATE commandes SET statut = :s WHERE id = :id")->execute([':s' => $_POST['statut'], ':id' => $id]);
    header('Location: ' . BASE_URL . '/admin/commande_detail.php?id=' . $id);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = :id");
$stmt->execute([':id' => $id]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: ' . BASE_URL . '/admin/commandes.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM commande_produits WHERE commande_id = :id");
$stmt->execute([':id' => $id]);
$lignes = $stmt->fetchAll();

$labelsStatut = [
    'en_attente' => 'Nouvelle', 'confirmee' => 'Confirmée', 'expediee' => 'Expédiée',
    'livree' => 'Livrée', 'annulee' => 'Annulée',
];

$pageAdminActive = 'commandes';
$titreAdmin = 'Commande ' . $commande['numero'];
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <div><h1>Commande <?= e($commande['numero']) ?></h1><p>Passée le <?= date('d/m/Y à H:i', strtotime($commande['created_at'])) ?></p></div>
  <a href="<?= BASE_URL ?>/admin/commandes.php" class="btn btn-contour">← Retour</a>
</div>

<div class="ligne-2" style="align-items:start;">
  <div class="panneau">
    <h3 style="margin-bottom:16px;">Produits commandés</h3>
    <table>
      <thead><tr><th>Produit</th><th>Prix unitaire</th><th>Qté</th><th>Sous-total</th></tr></thead>
      <tbody>
        <?php foreach ($lignes as $l): ?>
          <tr>
            <td><?= e($l['nom_produit']) ?></td>
            <td><?= formatPrix($l['prix_unitaire']) ?></td>
            <td><?= (int)$l['quantite'] ?></td>
            <td><?= formatPrix($l['prix_unitaire'] * $l['quantite']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div style="text-align:right; margin-top:16px; font-family:var(--font-display); font-size:1.2rem; color:var(--vert-foret);">
      Total : <?= formatPrix($commande['total']) ?>
    </div>

    <?php if (!empty($commande['note'])): ?>
      <h3 style="margin:24px 0 10px;">Note du client</h3>
      <p style="color:var(--encre-douce);"><?= nl2br(e($commande['note'])) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <div class="panneau">
      <h3 style="margin-bottom:16px;">Client</h3>
      <p><strong><?= e($commande['nom_client']) ?></strong></p>
      <p><?= e($commande['telephone']) ?></p>
      <?php if ($commande['email']): ?><p><?= e($commande['email']) ?></p><?php endif; ?>
      <p style="margin-top:10px;"><?= e($commande['adresse']) ?>, <?= e($commande['ville']) ?></p>
      <a href="https://wa.me/<?= preg_replace('/\D/', '', $commande['telephone']) ?>" target="_blank" class="btn btn-contour btn-petit" style="margin-top:14px;">Contacter sur WhatsApp</a>
    </div>

    <div class="panneau">
      <h3 style="margin-bottom:16px;">Statut de la commande</h3>
      <form method="post">
        <div class="groupe-champ">
          <select name="statut" onchange="this.form.submit()">
            <?php foreach ($labelsStatut as $cle => $label): ?>
              <option value="<?= $cle ?>" <?= $commande['statut'] === $cle ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
      <p class="aide" style="color:var(--encre-douce); font-size:0.82rem;">Paiement choisi : <?= e(str_replace('_', ' ', ucfirst($commande['mode_paiement']))) ?></p>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
