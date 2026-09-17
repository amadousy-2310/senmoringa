<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = getPDO();
    $pdo->prepare("DELETE FROM temoignages WHERE id = :id")->execute([':id' => $id]);
}

header('Location: ' . BASE_URL . '/admin/temoignages.php');
exit;
