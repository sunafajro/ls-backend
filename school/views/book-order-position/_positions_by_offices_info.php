<?php
/**
 * @var int     $bookId
 * @var int     $bookOrderId
 * @var int     $officeId
 * @var array   $offices
 */

use school\models\BookOrderPosition;

/** @var BookOrderPosition $position */
$position = BookOrderPosition::find()->andWhere([
    'book_id'       => $bookId,
    'book_order_id' => $bookOrderId,
    'office_id'     => $officeId,
    'visible'       => 1,
])->one();
$collapseId = "js--office-position-items-by-student-{$officeId}-{$bookId}";
?>
<div style="margin-bottom: 10px">
    <a
        href="#<?= $collapseId ?>"
        role="button"
        data-toggle="collapse"
        aria-expanded="false"
        aria-controls="<?= $collapseId ?>"
        class="text-info collapsed"><?= $offices[$officeId] ?></a>
    <div class="collapse small" id="<?= $collapseId ?>" aria-expanded="false">
        <?= $this->render('_position_items_by_student', [
                'position' => $position
        ]) ?>
    </div>
    <div class="small">
        <b>Итого:</b> <?= $position->count ?> шт. / <?= number_format($position->paid, 2, '.', ' ') ?> <i class="fa fa-rub" aria-hidden="true"></i>
    </div>
</div>
