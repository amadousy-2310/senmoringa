<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantites']) && is_array($_POST['quantites'])) {
    foreach ($_POST['quantites'] as $produitId => $quantite) {
        panierModifier((int)$produitId, (int)$quantite);
    }
}

header('Location: ' . BASE_URL . '/panier.php');
exit;
