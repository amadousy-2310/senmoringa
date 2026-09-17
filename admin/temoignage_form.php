<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPDO();
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$temoignage = $id ? getTemoignageParId($id) : null;
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $note = (int)($_POST['note'] ?? 5);
    $texte = trim($_POST['texte'] ?? '');
    $ordre = (int)($_POST['ordre'] ?? 0);
    $actif = isset($_POST['actif']) ? 1 : 0;

    if ($nom === '') $erreurs[] = "Le nom du client est obligatoire.";
    if ($texte === '') $erreurs[] = "Le texte du témoignage est obligatoire.";
    if ($note < 1 || $note > 5) $erreurs[] = "La note doit être comprise entre 1 et 5.";

    if (empty($erreurs)) {
        if ($temoignage) {
            $stmt = $pdo->prepare("UPDATE temoignages SET nom=:nom, ville=:ville, note=:note, texte=:texte, ordre=:ordre, actif=:actif WHERE id=:id");
            $stmt->execute([
                ':nom' => $nom, ':ville' => $ville ?: null, ':note' => $note, ':texte' => $texte,
                ':ordre' => $ordre, ':actif' => $actif, ':id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO temoignages (nom, ville, note, texte, ordre, actif) VALUES (:nom, :ville, :note, :texte, :ordre, :actif)");
            $stmt->execute([
                ':nom' => $nom, ':ville' => $ville ?: null, ':note' => $note, ':texte' => $texte,
                ':ordre' => $ordre, ':actif' => $actif,
            ]);
        }
        header('Location: ' . BASE_URL . '/admin/temoignages.php');
        exit;
    }
}

$v = fn($champ, $defaut = '') => e((string)($_POST[$champ] ?? ($temoignage[$champ] ?? $defaut)));

$pageAdminActive = 'temoignages';
$titreAdmin = $temoignage ? 'Modifier un témoignage' : 'Ajouter un témoignage';
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
  <div><h1><?= $temoignage ? 'Modifier le témoignage' : 'Ajouter un témoignage' ?></h1></div>
  <a href="<?= BASE_URL ?>/admin/temoignages.php" class="btn btn-contour">← Retour à la liste</a>
</div>

<?php if (!empty($erreurs)): ?>
  <div class="alerte alerte-erreur"><?php foreach ($erreurs as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
<?php endif; ?>

<form method="post" class="panneau">
  <input type="hidden" name="id" value="<?= (int)$id ?>">

  <div class="ligne-2">
    <div class="groupe-champ">
      <label for="nom">Nom du client</label>
      <input type="text" id="nom" name="nom" value="<?= $v('nom') ?>" required>
    </div>
    <div class="groupe-champ">
      <label for="ville">Ville (optionnel)</label>
      <input type="text" id="ville" name="ville" value="<?= $v('ville') ?>">
    </div>
  </div>

  <div class="groupe-champ">
    <label for="texte">Témoignage</label>
    <textarea id="texte" name="texte" rows="4" required><?= $v('texte') ?></textarea>
    <p class="aide">Le texte s'affiche automatiquement entre guillemets sur le site.</p>
  </div>

  <div class="ligne-2">
    <div class="groupe-champ">
      <label for="note">Note</label>
      <select id="note" name="note">
        <?php for ($n = 5; $n >= 1; $n--): ?>
          <option value="<?= $n ?>" <?= ((int)($temoignage['note'] ?? 5) === $n) ? 'selected' : '' ?>><?= str_repeat('★', $n) ?> (<?= $n ?>/5)</option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="groupe-champ">
      <label for="ordre">Ordre d'affichage</label>
      <input type="number" id="ordre" name="ordre" step="1" value="<?= $v('ordre', 0) ?>">
      <p class="aide">Les plus petits chiffres s'affichent en premier.</p>
    </div>
  </div>

  <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $actifCoche = isset($_POST['actif']);
    } else {
        $actifCoche = $temoignage ? !empty($temoignage['actif']) : true;
    }
  ?>
  <div class="groupe-champ checkbox-inline">
    <input type="checkbox" id="actif" name="actif" <?= $actifCoche ? 'checked' : '' ?>>
    <label for="actif" style="margin:0;">Visible sur la page d'accueil</label>
  </div>

  <button type="submit" class="btn btn-principal"><?= $temoignage ? 'Enregistrer les modifications' : 'Créer le témoignage' ?></button>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
