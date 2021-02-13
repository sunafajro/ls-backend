<?php
/**
 * @var View              $this
 * @var BookOrderPosition $position
 */

use school\models\BookOrderPosition;
use school\models\BookOrderPositionItem;
use yii\helpers\Html;
use yii\web\View;

if (!empty($position)) {
    $paymentTypes = (new BookOrderPositionItem)->getPaymentTypes();
    /** @var BookOrderPositionItem $item */
    foreach($position->getBookOrderPositionItems()->andWhere(['visible' => 1])->all() ?? [] as $item) {?>
        <div class="well well-sm" style="padding:5px;margin-bottom:5px">
            <?= $item->student_name ?>
            <br />
            <?= $item->count ?> шт. /
            <?= number_format($item->paid, 2, '.', ' ') ?> <i class="fa fa-rub" aria-hidden="true"></i> /
            <i
                class="fa fa-<?= $item->payment_type === BookOrderPositionItem::PAYMENT_TYPE_CASH ? 'money' : 'university' ?>"
                title="<?= $paymentTypes[$item->payment_type] ?>"></i> /
            <i class="fa fa-info" title="<?= Html::encode($item->payment_comment) ?>"></i>
        </div>
    <?php }
}