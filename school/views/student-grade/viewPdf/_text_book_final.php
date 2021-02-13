<?php

use school\models\StudentGrade;
use yii\web\View;

/**
 * @var View  $this
 * @var array $attestation
 * @var array $contents
 * @var array $exams
 */
?>
<div class="text-description-block-slim">
    сдал(а) экзамен и получил(а)
</div>
<div class="text-result-block-slim">
    <?= $attestation['score'] ?>
</div>
<div class="text-description-block-slim">
    по прохождении курса
</div>
<div class="text-result-block-slim">
    <?= $contents[StudentGrade::EXAM_CONTENT_TOOK_THE_COURSE] ?? '' ?>
</div>
<div class="text-description-block-slim">
    по учебнику
</div>
<div class="text-result-block-slim">
    <?= $contents[StudentGrade::EXAM_CONTENT_ACCORDING_TO_BOOK] ?? '' ?>
</div>
<div class="text-description-block-slim">
    в количестве часов
</div>
<div class="text-result-block-slim">
    <?= $contents[StudentGrade::EXAM_CONTENT_COURSE_HOURS_COUNT] ?? '' ?>
</div>