<?php

use app\models\BookOrder;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View      $this
 * @var BookOrder $bookOrder
 * @var array     $bookOrderCounters
 * @var bool      $current
 */
?>
<?php if ($current) { ?>
    <h4>Текущий заказ:</h4>
<?php } else { ?>
    <h4>Заказ №<?= $bookOrder->id ?></h4>
<?php } ?>
<?php if (!empty($bookOrder)) { ?>
    <p>
        <b><?= Yii::t('app', 'Start date') ?>:</b> <?= date('d.m.Y', strtotime($bookOrder->date_start)) ?>
    </p>
    <p>
        <b><?= Yii::t('app', 'End date') ?></b> <?= date('d.m.Y', strtotime($bookOrder->date_end)) ?>
    </p>
    <p><b>Количество позиций:</b> <?= Html::a(
            $bookOrder->positionsCount ?? 0,
            ['book-order-position/index', 'id' => $bookOrder->id]
        ) ?></p>
    <p>
        <b><?= Yii::t('app', 'Total books count') ?>:</b> <?= $bookOrderCounters['total_count'] ?? 0 ?> шт.
    </p>
    <?php if (in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) { ?>
        <p><b>Итого (закупочная цена):</b> <?= $bookOrderCounters['total_purchase_cost'] ?? 0 ?> р.</p>
    <?php } ?>
    <p>
        <b><?= Yii::t('app', 'Total') ?>:</b> <?= $bookOrderCounters['total_selling_cost'] ?? 0 ?> р.
    </p>
<?php } else { ?>
    <i>В данный момент нет открытых заказов учебников...</i>
<?php } ?>