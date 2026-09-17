<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPDO();

// Activer / désactiver rapidement un produit
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE produits SET actif = 1 - actif WHERE id = :id")->execute([':id' => $id]);
    header('Location: ' . BASE_URL . '/admin/produits.php');
    exit;
}

$produits = $pdo->query("SELECT p.*, c.nom AS categorie_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.created_at DESC")->fetchAll();

$pageAdminActive = 'produits';
$titreAdmin = 'Produits';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <div><h1>Produits</h1><p><?= count($produits) ?> produit(s) au catalogue.</p></div>
  <a href="<?= BASE_URL ?>/admin/produit_form.php" class="btn btn-principal">+ Ajouter un produit</a>
</div>

<div class="panneau">
  <table>
    <thead><tr><th></th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Statut</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($produits as $p): ?>
        <tr>
          <td>
            <div class="miniature-table">
              <?php if (!empty($p['image'])): ?>
                <img src="<?= BASE_URL ?>/assets/images/produits/<?= e($p['image']) ?>" alt="">
              <?php else: ?>🌿<?php endif; ?>
            </div>
          </td>
          <td><strong><?= e($p['nom']) ?></strong></td>
          <td><?= e($p['categorie_nom'] ?? '—') ?></td>
          <td><?= formatPrix($p['prix']) ?></td>
          <td><?= (int)$p['stock'] ?><?= $p['stock'] <= 5 ? ' <span class="badge badge-jaune">bas</span>' : '' ?></td>
          <td><span class="badge <?= $p['actif'] ? 'badge-vert' : 'badge-gris' ?>"><?= $p['actif'] ? 'Actif' : 'Masqué' ?></span></td>
          <td>
            <div class="actions-table">
              <a href="<?= BASE_URL ?>/admin/produit_form.php?id=<?= (int)$p['id'] ?>" class="btn btn-contour btn-petit">Modifier</a>
              <a href="<?= BASE_URL ?>/admin/produits.php?toggle=<?= (int)$p['id'] ?>" class="btn btn-contour btn-petit"><?= $p['actif'] ? 'Masquer' : 'Activer' ?></a>
              <a href="<?= BASE_URL ?>/admin/produit_supprimer.php?id=<?= (int)$p['id'] ?>" class="btn btn-danger btn-petit" onclick="return confirm('Supprimer définitivement ce produit ?');">Supprimer</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($produits)): ?><tr><td colspan="7">Aucun produit pour le moment.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
