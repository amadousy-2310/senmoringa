<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$produitId = (int)($_GET['id'] ?? 0);
if ($produitId > 0) {
    panierSupprimer($produitId);
}

header('Location: ' . BASE_URL . '/panier.php');
exit;
