<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$estAjax = (
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
);

$succes = false;
$message = "Ce produit n'est plus disponible.";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produitId = (int)($_POST['produit_id'] ?? 0);
    $quantite  = max(1, (int)($_POST['quantite'] ?? 1));
    $produit = getProduitParId($produitId);

    if ($produit && $produit['stock'] > 0) {
        panierAjouter($produitId, $quantite);
        $message = $produit['nom'] . ' a été ajouté au panier.';
        $succes = true;
        $_SESSION['message_panier'] = e($message);
    }
}

if ($estAjax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $succes,
        'message' => $message,
        'count'   => panierNombreArticles(),
    ]);
    exit;
}

$retour = $_POST['retour'] ?? (BASE_URL . '/boutique.php');
header('Location: ' . $retour);
exit;
