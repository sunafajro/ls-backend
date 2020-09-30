<?php

use app\models\StudentGrade;
use yii\web\View;

/**
 * @var View  $this
 * @var array $attestation
 * @var array $contents
 * @var array $exams
 */
?>
<div class="text-description-block">
    написала(а)
</div>
<div class="text-result-block">
    <?= $contents[StudentGrade::EXAM_CONTENT_WROTE_AN] ?? '' ?>
</div>
<div class="text-description-block">
    стал(а)
</div>
<div class="text-result-block">
    <?= $contents[StudentGrade::EXAM_CONTENT_BECAME_WHO] ?? '' ?>
</div>
<div class="text-description-block">
    итог/кол.-во баллов
</div>
<div class="text-result-block">
    <?= $attestation['score'] ?>
</div>