<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPDO();
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$produit = $id ? getProduitParId($id) : null;
$categories = getCategories();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $descriptionCourte = trim($_POST['description_courte'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = (float)str_replace(',', '.', $_POST['prix'] ?? 0);
    $ancienPrix = trim($_POST['ancien_prix'] ?? '') !== '' ? (float)str_replace(',', '.', $_POST['ancien_prix']) : null;
    $stock = (int)($_POST['stock'] ?? 0);
    $categorieId = (int)($_POST['categorie_id'] ?? 0) ?: null;
    $populaire = isset($_POST['populaire']) ? 1 : 0;
    $actif = isset($_POST['actif']) ? 1 : 0;

    if ($nom === '') $erreurs[] = "Le nom du produit est obligatoire.";
    if ($prix <= 0) $erreurs[] = "Le prix doit être supérieur à 0.";
    if ($stock < 0) $erreurs[] = "Le stock ne peut pas être négatif.";

    // Gestion de l'image
    $nomImage = $produit['image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
        $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensionsAutorisees, true)) {
            $erreurs[] = "L'image doit être au format JPG, PNG ou WEBP.";
        } elseif ($_FILES['image']['size'] > 4 * 1024 * 1024) {
            $erreurs[] = "L'image ne doit pas dépasser 4 Mo.";
        } else {
            $nomImage = 'produit_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $extension;
            $dossier = __DIR__ . '/../assets/images/produits/';
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $dossier . $nomImage)) {
                $erreurs[] = "Le téléversement de l'image a échoué.";
                $nomImage = $produit['image'] ?? null;
            }
        }
    }

    if (empty($erreurs)) {
        $slugBase = slugify($nom);
        $slug = $slugBase;
        $i = 1;
        do {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE slug = :slug AND id != :id");
            $stmt->execute([':slug' => $slug, ':id' => $id ?: 0]);
            $existe = $stmt->fetchColumn();
            if ($existe) { $slug = $slugBase . '-' . (++$i); }
        } while ($existe);

        if ($produit) {
            $stmt = $pdo->prepare("UPDATE produits SET nom=:nom, slug=:slug, description=:desc, description_courte=:descc,
                                    prix=:prix, ancien_prix=:ancien, image=:image, stock=:stock, categorie_id=:cat,
                                    populaire=:pop, actif=:actif WHERE id=:id");
            $stmt->execute([
                ':nom' => $nom, ':slug' => $slug, ':desc' => $description, ':descc' => $descriptionCourte,
                ':prix' => $prix, ':ancien' => $ancienPrix, ':image' => $nomImage, ':stock' => $stock,
                ':cat' => $categorieId, ':pop' => $populaire, ':actif' => $actif, ':id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO produits (nom, slug, description, description_courte, prix, ancien_prix, image, stock, categorie_id, populaire, actif)
                                    VALUES (:nom, :slug, :desc, :descc, :prix, :ancien, :image, :stock, :cat, :pop, :actif)");
            $stmt->execute([
                ':nom' => $nom, ':slug' => $slug, ':desc' => $description, ':descc' => $descriptionCourte,
                ':prix' => $prix, ':ancien' => $ancienPrix, ':image' => $nomImage, ':stock' => $stock,
                ':cat' => $categorieId, ':pop' => $populaire, ':actif' => $actif,
            ]);
        }
        header('Location: ' . BASE_URL . '/admin/produits.php');
        exit;
    }
}

$v = fn($champ, $defaut = '') => e($_POST[$champ] ?? ($produit[$champ] ?? $defaut));

$pageAdminActive = 'produits';
$titreAdmin = $produit ? 'Modifier un produit' : 'Ajouter un produit';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <div><h1><?= $produit ? 'Modifier le produit' : 'Ajouter un produit' ?></h1></div>
  <a href="<?= BASE_URL ?>/admin/produits.php" class="btn btn-contour">← Retour à la liste</a>
</div>

<?php if (!empty($erreurs)): ?>
  <div class="alerte alerte-erreur"><?php foreach ($erreurs as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="panneau">
  <input type="hidden" name="id" value="<?= (int)$id ?>">

  <div class="groupe-champ">
    <label for="nom">Nom du produit</label>
    <input type="text" id="nom" name="nom" value="<?= $v('nom') ?>" required>
  </div>

  <div class="ligne-2">
    <div class="groupe-champ">
      <label for="categorie_id">Catégorie</label>
      <select id="categorie_id" name="categorie_id">
        <option value="">— Aucune —</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int)$cat['id'] ?>" <?= (($produit['categorie_id'] ?? null) == $cat['id']) ? 'selected' : '' ?>><?= e($cat['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="groupe-champ">
      <label for="image">Photo du produit</label>
      <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
      <p class="aide">Sans photo, une illustration générique sera affichée automatiquement.</p>
    </div>
  </div>

  <div class="groupe-champ">
    <label for="description_courte">Description courte (affichée sur les cartes produit)</label>
    <input type="text" id="description_courte" name="description_courte" value="<?= $v('description_courte') ?>" maxlength="255">
  </div>

  <div class="groupe-champ">
    <label for="description">Description complète</label>
    <textarea id="description" name="description" rows="5"><?= $v('description') ?></textarea>
  </div>

  <div class="ligne-3">
    <div class="groupe-champ">
      <label for="prix">Prix (FCFA)</label>
      <input type="number" id="prix" name="prix" step="1" min="0" value="<?= $v('prix') ?>" required>
    </div>
    <div class="groupe-champ">
      <label for="ancien_prix">Ancien prix (optionnel, pour affichage promo)</label>
      <input type="number" id="ancien_prix" name="ancien_prix" step="1" min="0" value="<?= $v('ancien_prix') ?>">
    </div>
    <div class="groupe-champ">
      <label for="stock">Stock disponible</label>
      <input type="number" id="stock" name="stock" step="1" min="0" value="<?= $v('stock', 0) ?>" required>
    </div>
  </div>

  <div class="groupe-champ checkbox-inline">
    <input type="checkbox" id="populaire" name="populaire" <?= !empty($produit['populaire']) || (isset($_POST['populaire'])) ? 'checked' : '' ?>>
    <label for="populaire" style="margin:0;">Mettre en avant comme produit populaire</label>
  </div>
  <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $actifCoche = isset($_POST['actif']);
    } else {
        $actifCoche = $produit ? !empty($produit['actif']) : true; // coché par défaut pour un nouveau produit
    }
  ?>
  <div class="groupe-champ checkbox-inline">
    <input type="checkbox" id="actif" name="actif" <?= $actifCoche ? 'checked' : '' ?>>
    <label for="actif" style="margin:0;">Visible sur la boutique</label>
  </div>

  <button type="submit" class="btn btn-principal"><?= $produit ? 'Enregistrer les modifications' : 'Créer le produit' ?></button>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
