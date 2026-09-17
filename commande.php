<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/svg.php';

$lignes = panierContenu();
if (empty($lignes)) {
    header('Location: ' . BASE_URL . '/panier.php');
    exit;
}

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $modePaiement = $_POST['mode_paiement'] ?? '';
    $note = trim($_POST['note'] ?? '');

    if ($nom === '') $erreurs[] = "Le nom complet est obligatoire.";
    if ($telephone === '') $erreurs[] = "Le numéro de téléphone est obligatoire.";
    if ($adresse === '') $erreurs[] = "L'adresse de livraison est obligatoire.";
    if ($ville === '') $erreurs[] = "La ville est obligatoire.";
    if (!in_array($modePaiement, ['orange_money', 'wave', 'livraison'], true)) $erreurs[] = "Choisissez un mode de paiement.";

    if (empty($erreurs)) {
        $pdo = getPDO();
        $fraisLivraison = (mb_strtolower($ville) === 'dakar') ? FRAIS_LIVRAISON_DAKAR : FRAIS_LIVRAISON_AUTRE;
        $sousTotal = panierTotal();
        $total = $sousTotal + $fraisLivraison;
        $numero = genererNumeroCommande();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO commandes (numero, nom_client, telephone, email, adresse, ville, mode_paiement, note, total, statut)
                                    VALUES (:numero, :nom, :tel, :email, :adresse, :ville, :paiement, :note, :total, 'en_attente')");
            $stmt->execute([
                ':numero' => $numero, ':nom' => $nom, ':tel' => $telephone, ':email' => $email ?: null,
                ':adresse' => $adresse, ':ville' => $ville, ':paiement' => $modePaiement,
                ':note' => $note ?: null, ':total' => $total,
            ]);
            $commandeId = $pdo->lastInsertId();

            $stmtLigne = $pdo->prepare("INSERT INTO commande_produits (commande_id, produit_id, nom_produit, prix_unitaire, quantite)
                                         VALUES (:cid, :pid, :nom, :prix, :qte)");
            $stmtStock = $pdo->prepare("UPDATE produits SET stock = stock - :qte WHERE id = :pid AND stock >= :qte2");

            foreach ($lignes as $ligne) {
                $p = $ligne['produit'];
                $stmtLigne->execute([
                    ':cid' => $commandeId, ':pid' => $p['id'], ':nom' => $p['nom'],
                    ':prix' => $p['prix'], ':qte' => $ligne['quantite'],
                ]);
                $stmtStock->execute([':qte' => $ligne['quantite'], ':pid' => $p['id'], ':qte2' => $ligne['quantite']]);
            }

            $pdo->commit();
            panierVider();
            $_SESSION['derniere_commande'] = $numero;
            header('Location: ' . BASE_URL . '/merci.php');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $erreurs[] = "Une erreur est survenue lors de l'enregistrement de votre commande. Veuillez réessayer.";
        }
    }
}

$total = panierTotal();
$pageActive = '';
$titrePage = 'Finaliser ma commande — ' . SITE_NOM;
require __DIR__ . '/includes/header.php';
?>

<div class="conteneur">
  <p class="fil-ariane"><a href="<?= BASE_URL ?>/index.php">Accueil</a> / <a href="<?= BASE_URL ?>/panier.php">Panier</a> / Commande</p>
</div>

<section class="page-commande">
  <div class="conteneur">
    <h1 style="margin-bottom: 30px;">Finaliser ma commande</h1>

    <?php if (!empty($erreurs)): ?>
      <div class="alerte alerte-erreur">
        <?php foreach ($erreurs as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="mise-en-page-commande">
      <div>
        <h3 style="margin-bottom: 20px;">Vos informations</h3>

        <div class="groupe-champ">
          <label for="nom">Nom complet</label>
          <input type="text" id="nom" name="nom" value="<?= e($_POST['nom'] ?? '') ?>" required>
        </div>

        <div class="ligne-2">
          <div class="groupe-champ">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="<?= e($_POST['telephone'] ?? '') ?>" placeholder="77 000 00 00" required>
          </div>
          <div class="groupe-champ">
            <label for="email">Email (optionnel)</label>
            <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="groupe-champ">
          <label for="adresse">Adresse de livraison</label>
          <input type="text" id="adresse" name="adresse" value="<?= e($_POST['adresse'] ?? '') ?>" placeholder="Quartier, rue, numéro..." required>
        </div>

        <div class="groupe-champ">
          <label for="ville">Ville</label>
          <input type="text" id="ville" name="ville" value="<?= e($_POST['ville'] ?? '') ?>" placeholder="Dakar, Thiès, Saint-Louis..." required>
        </div>

        <div class="groupe-champ">
          <label>Mode de paiement</label>
          <div class="options-paiement">
            <label class="option-paiement">
              <input type="radio" name="mode_paiement" value="orange_money" <?= (($_POST['mode_paiement'] ?? '') === 'orange_money') ? 'checked' : '' ?> required>
              <span><strong>Orange Money</strong><span>Paiement au numéro communiqué après confirmation</span></span>
            </label>
            <label class="option-paiement">
              <input type="radio" name="mode_paiement" value="wave" <?= (($_POST['mode_paiement'] ?? '') === 'wave') ? 'checked' : '' ?>>
              <span><strong>Wave</strong><span>Paiement au numéro communiqué après confirmation</span></span>
            </label>
            <label class="option-paiement">
              <input type="radio" name="mode_paiement" value="livraison" <?= (($_POST['mode_paiement'] ?? '') === 'livraison') ? 'checked' : '' ?>>
              <span><strong>Paiement à la livraison</strong><span>Réglez en espèces à la réception</span></span>
            </label>
          </div>
        </div>

        <div class="groupe-champ">
          <label for="note">Note pour la livraison (optionnel)</label>
          <textarea id="note" name="note" rows="3" placeholder="Repère, horaire préféré..."><?= e($_POST['note'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-principal btn-bloc">Confirmer ma commande</button>
      </div>

      <aside class="recap-panier">
        <h3>Votre commande</h3>
        <?php foreach ($lignes as $ligne): ?>
          <div class="recap-commande-item">
            <span><?= (int)$ligne['quantite'] ?> × <?= e($ligne['produit']['nom']) ?></span>
            <span><?= formatPrix($ligne['sous_total']) ?></span>
          </div>
        <?php endforeach; ?>
        <div class="ligne-recap"><span>Sous-total</span><span><?= formatPrix($total) ?></span></div>
        <div class="ligne-recap"><span>Livraison</span><span>1 500 – 3 000 FCFA selon la ville</span></div>
        <div class="ligne-recap total"><span>Total estimé</span><span><?= formatPrix($total) ?> + livraison</span></div>
      </aside>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
