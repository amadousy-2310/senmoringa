<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

// ---- Protection anti-brute-force ----
const MAX_TENTATIVES = 5;          // nombre d'essais autorisés
const DUREE_BLOCAGE_MINUTES = 15;  // durée du blocage une fois le seuil atteint

$ip = $_SERVER['REMOTE_ADDR'] ?? 'inconnu';
$pdo = getPDO();
$erreur = '';

$stmt = $pdo->prepare("SELECT * FROM admin_tentatives_connexion WHERE adresse_ip = :ip LIMIT 1");
$stmt->execute([':ip' => $ip]);
$tentative = $stmt->fetch();

$bloque = false;
$minutesRestantes = 0;
if ($tentative && $tentative['bloque_jusqua'] && strtotime($tentative['bloque_jusqua']) > time()) {
    $bloque = true;
    $minutesRestantes = (int) ceil((strtotime($tentative['bloque_jusqua']) - time()) / 60);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($bloque) {
        $erreur = "Trop de tentatives échouées. Réessayez dans {$minutesRestantes} minute(s).";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmtAdmin = $pdo->prepare("SELECT * FROM admin_users WHERE username = :u LIMIT 1");
        $stmtAdmin->execute([':u' => $username]);
        $admin = $stmtAdmin->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Connexion réussie : on efface le compteur de tentatives pour cette IP
            $pdo->prepare("DELETE FROM admin_tentatives_connexion WHERE adresse_ip = :ip")->execute([':ip' => $ip]);

            // On régénère l'identifiant de session pour empêcher le vol / la fixation de session
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        }

        // Échec : on incrémente (ou on initialise) le compteur de tentatives pour cette IP
        $fenetreExpiree = $tentative && strtotime($tentative['derniere_tentative']) < (time() - DUREE_BLOCAGE_MINUTES * 60);

        if (!$tentative || $fenetreExpiree) {
            $pdo->prepare("
                INSERT INTO admin_tentatives_connexion (adresse_ip, tentatives, derniere_tentative, bloque_jusqua)
                VALUES (:ip, 1, NOW(), NULL)
                ON DUPLICATE KEY UPDATE tentatives = 1, derniere_tentative = NOW(), bloque_jusqua = NULL
            ")->execute([':ip' => $ip]);
        } else {
            $nouvellesTentatives = $tentative['tentatives'] + 1;
            $bloqueJusqua = $nouvellesTentatives >= MAX_TENTATIVES
                ? date('Y-m-d H:i:s', time() + DUREE_BLOCAGE_MINUTES * 60)
                : null;
            $pdo->prepare("
                UPDATE admin_tentatives_connexion
                SET tentatives = :t, derniere_tentative = NOW(), bloque_jusqua = :b
                WHERE adresse_ip = :ip
            ")->execute([':t' => $nouvellesTentatives, ':b' => $bloqueJusqua, ':ip' => $ip]);

            if ($bloqueJusqua !== null) {
                $bloque = true;
                $minutesRestantes = DUREE_BLOCAGE_MINUTES;
            }
        }

        $erreur = $bloque
            ? "Trop de tentatives échouées. Réessayez dans {$minutesRestantes} minute(s)."
            : "Identifiant ou mot de passe incorrect.";
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion admin — SenMoringa</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="page-login">
  <div class="carte-login">
    <div class="admin-logo">🌿 SenMoringa</div>
    <p class="sous-titre">Espace d'administration</p>
    <?php if ($erreur): ?><div class="alerte alerte-erreur"><?= e($erreur) ?></div><?php endif; ?>
    <form method="post">
      <div class="groupe-champ">
        <label for="username">Identifiant</label>
        <input type="text" id="username" name="username" required autofocus <?= $bloque ? 'disabled' : '' ?>>
      </div>
      <div class="groupe-champ">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required <?= $bloque ? 'disabled' : '' ?>>
      </div>
      <button type="submit" class="btn btn-principal" style="width:100%; justify-content:center;" <?= $bloque ? 'disabled' : '' ?>>Se connecter</button>
    </form>
  </div>
</div>
</body>
</html>
