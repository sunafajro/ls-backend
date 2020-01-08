<?php

use yii\web\View;

/**
 * @var View  $this
 * @var array $attestation
 * @var array $contents
 * @var array $exams
 */
$preparedContents = [];
foreach($contents ?? [] as $key => $value) {
    $preparedContents[] = ($contentTypes[$key] ?? $key) . ': ' . $value;
}
?>
<div class="text-description-block">
    сдал/а
</div>
<div class="text-result-block">
    <?= $exams[$attestation['description']] ?? $attestation['description'] ?>
</div>
<div class="text-description-block">
    с результатом
</div>
<div class="text-result-block">
    <?= implode(', ', $preparedContents); ?>
</div>
<div class="text-description-block">
    итог/уровень
</div>
<div class="text-result-block">
    <?= $attestation['score'] ?>
</div>