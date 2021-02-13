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
<div class="text-description-block">
    принял(а) участие в
</div>
<div class="text-result-block">
    <?= $contents[StudentGrade::EXAM_CONTENT_TOOK_PART_IN] ?? '' ?>
</div>
<div class="text-description-block">
    стал(а)
</div>
<div class="text-result-block">
    <?= $contents[StudentGrade::EXAM_CONTENT_BECAME_WHO] ?? '' ?>
</div>
<?php if ($attestation['score'] ?? false) { ?>
    <div class="text-description-block">
        получил(а) баллов
    </div>
    <div class="text-result-block">
        <?= $attestation['score'] ?>
    </div>
<?php } else { ?>
    <div class="empty-block-20"></div>
<?php } ?>