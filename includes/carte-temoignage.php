<div class="carte-temoignage">
  <div class="etoiles"><?= str_repeat('★', max(1, min(5, (int)$t['note']))) . str_repeat('☆', 5 - max(1, min(5, (int)$t['note']))) ?></div>
  <p class="texte">« <?= e($t['texte']) ?> »</p>
  <div class="auteur">
    <span class="avatar-initiale"><?= e(mb_strtoupper(mb_substr($t['nom'], 0, 1))) ?></span>
    <?= e($t['nom']) ?><?= !empty($t['ville']) ? ', ' . e($t['ville']) : '' ?>
  </div>
</div>
