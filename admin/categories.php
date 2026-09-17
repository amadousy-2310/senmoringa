<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPDO();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    $nom = trim($_POST['nom'] ?? '');
    if ($nom === '') {
        $erreurs[] = "Le nom de la catégorie est obligatoire.";
    } else {
        $slug = slugify($nom);
        $stmt = $pdo->prepare("INSERT INTO categories (nom, slug, description) VALUES (:nom, :slug, :desc)");
        try {
            $stmt->execute([':nom' => $nom, ':slug' => $slug, ':desc' => trim($_POST['description'] ?? '')]);
        } catch (PDOException $e) {
            $erreurs[] = "Cette catégorie existe déjà.";
        }
    }
}

if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $pdo->prepare("DELETE FROM categories WHERE id = :id")->execute([':id' => $id]);
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM produits p WHERE p.categorie_id = c.id) AS nb_produits FROM categories c ORDER BY c.nom")->fetchAll();

$pageAdminActive = 'categories';
$titreAdmin = 'Catégories';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <div><h1>Catégories</h1><p>Organisez vos produits (poudre, huile, jus, compléments...).</p></div>
</div>

<?php if (!empty($erreurs)): ?>
  <div class="alerte alerte-erreur"><?php foreach ($erreurs as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
<?php endif; ?>

<div class="panneau">
  <div class="panneau-tete"><h3>Ajouter une catégorie</h3></div>
  <form method="post" class="ligne-2" style="align-items:end;">
    <input type="hidden" name="action" value="ajouter">
    <div class="groupe-champ" style="margin-bottom:0;">
      <label for="nom">Nom</label>
      <input type="text" id="nom" name="nom" required>
    </div>
    <div class="groupe-champ" style="margin-bottom:0;">
      <label for="description">Description (optionnel)</label>
      <input type="text" id="description" name="description">
    </div>
    <div class="groupe-champ" style="margin-bottom:0; grid-column: 1 / -1;">
      <button type="submit" class="btn btn-principal">Ajouter</button>
    </div>
  </form>
</div>

<div class="panneau">
  <table>
    <thead><tr><th>Nom</th><th>Slug</th><th>Produits</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
        <tr>
          <td><strong><?= e($cat['nom']) ?></strong></td>
          <td><?= e($cat['slug']) ?></td>
          <td><?= (int)$cat['nb_produits'] ?></td>
          <td>
            <a href="<?= BASE_URL ?>/admin/categories.php?supprimer=<?= (int)$cat['id'] ?>" class="btn btn-danger btn-petit"
               onclick="return confirm('Supprimer cette catégorie ? Les produits associés resteront mais sans catégorie.');">Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
