<?php
$pageAdminActive = $pageAdminActive ?? '';
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($titreAdmin ?? 'Administration') ?> — SenMoringa</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-logo">🌿 SenMoringa</div>
    <nav class="admin-nav">
      <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= $pageAdminActive === 'dashboard' ? 'actif' : '' ?>">Tableau de bord</a>
      <a href="<?= BASE_URL ?>/admin/produits.php" class="<?= $pageAdminActive === 'produits' ? 'actif' : '' ?>">Produits</a>
      <a href="<?= BASE_URL ?>/admin/categories.php" class="<?= $pageAdminActive === 'categories' ? 'actif' : '' ?>">Catégories</a>
      <a href="<?= BASE_URL ?>/admin/commandes.php" class="<?= $pageAdminActive === 'commandes' ? 'actif' : '' ?>">Commandes</a>
      <a href="<?= BASE_URL ?>/admin/temoignages.php" class="<?= $pageAdminActive === 'temoignages' ? 'actif' : '' ?>">Témoignages</a>
    </nav>
    <div class="admin-sidebar-bas">
      <a href="<?= BASE_URL ?>/index.php" target="_blank">↗ Voir le site</a><br><br>
      <a href="<?= BASE_URL ?>/admin/logout.php">Se déconnecter</a>
    </div>
  </aside>
  <main class="admin-main">
