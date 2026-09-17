<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPDO();

// Activer / désactiver rapidement un témoignage
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE temoignages SET actif = 1 - actif WHERE id = :id")->execute([':id' => $id]);
    header('Location: ' . BASE_URL . '/admin/temoignages.php');
    exit;
}

$temoignages = $pdo->query("SELECT * FROM temoignages ORDER BY ordre ASC, created_at DESC")->fetchAll();

$pageAdminActive = 'temoignages';
$titreAdmin = 'Témoignages';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <div><h1>Témoignages</h1><p><?= count($temoignages) ?> témoignage(s) enregistré(s).</p></div>
  <a href="<?= BASE_URL ?>/admin/temoignage_form.php" class="btn btn-principal">+ Ajouter un témoignage</a>
</div>

<div class="panneau">
  <table>
    <thead><tr><th>Client</th><th>Note</th><th>Texte</th><th>Statut</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($temoignages as $t): ?>
        <tr>
          <td>
            <strong><?= e($t['nom']) ?></strong><?= !empty($t['ville']) ? '<br><span class="aide">' . e($t['ville']) . '</span>' : '' ?>
          </td>
          <td><?= str_repeat('★', (int)$t['note']) ?></td>
          <td style="max-width:360px;"><?= e(mb_strimwidth($t['texte'], 0, 90, '…')) ?></td>
          <td><span class="badge <?= $t['actif'] ? 'badge-vert' : 'badge-gris' ?>"><?= $t['actif'] ? 'Visible' : 'Masqué' ?></span></td>
          <td>
            <div class="actions-table">
              <a href="<?= BASE_URL ?>/admin/temoignage_form.php?id=<?= (int)$t['id'] ?>" class="btn btn-contour btn-petit">Modifier</a>
              <a href="<?= BASE_URL ?>/admin/temoignages.php?toggle=<?= (int)$t['id'] ?>" class="btn btn-contour btn-petit"><?= $t['actif'] ? 'Masquer' : 'Activer' ?></a>
              <a href="<?= BASE_URL ?>/admin/temoignage_supprimer.php?id=<?= (int)$t['id'] ?>" class="btn btn-danger btn-petit" onclick="return confirm('Supprimer définitivement ce témoignage ?');">Supprimer</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($temoignages)): ?><tr><td colspan="5">Aucun témoignage pour le moment.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
