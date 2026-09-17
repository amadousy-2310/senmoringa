<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = getPDO();
    $produit = getProduitParId($id);
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = :id");
    $stmt->execute([':id' => $id]);
    if ($produit && !empty($produit['image'])) {
        $chemin = __DIR__ . '/../assets/images/produits/' . $produit['image'];
        if (is_file($chemin)) { @unlink($chemin); }
    }
}

header('Location: ' . BASE_URL . '/admin/produits.php');
exit;
