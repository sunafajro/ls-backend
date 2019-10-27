<?php

use app\models\BookOrder;
use Yii;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View      $this
 * @var BookOrder $bookOrder
 * @var array     $bookOrderCounters
 * @var bool      $current
 */
$roleId = Yii::$app->session->get('user.ustatus');
?>
<div>

    <?php if ($current) { ?>
        <h4>Текущий заказ:</h4>
    <?php } else { ?>
        <h4>Заказ №<?= $bookOrder->id ?></h4>
    <?php } ?>
    <?php if (!empty($bookOrder)) { ?>
        <div>
            <div>
                <b><?= Yii::t('app', 'Start date') ?>:</b> <?= date('d.m.Y', strtotime($bookOrder->date_start)) ?>
            </div>
            <div>
                <b><?= Yii::t('app', 'End date') ?></b> <?= date('d.m.Y', strtotime($bookOrder->date_end)) ?>
            </div>
            <div><b>Количество позиций:</b> <?= Html::a(
                                                    $bookOrderCounters['positionCount'] ?? 0,
                                                    ['book-order-position/index', 'id' => $bookOrder->id]
                                                ) ?></div>
            <div>
                <b><?= Yii::t('app', 'Total books count') ?>:</b> <?= $bookOrderCounters['total_count'] ?? 0 ?> шт.
            </div>
            <?php if (in_array((int) Yii::$app->session->get('user.ustatus'), [3, 7])) { ?>
                <div><b>Итого (закупочная цена):</b> <?= $bookOrderCounters['total_purchase_cost'] ?? 0 ?> р.</div>
            <?php } ?>
            <div>
                <b><?= Yii::t('app', 'Total') ?>:</b> <?= $bookOrderCounters['total_selling_cost'] ?? 0 ?> р.
            </div>
        </div>
        <?php if (!empty($bookOrderCounters['positions'] ?? [])) { ?>
            <h4>Позиции заказа:</h4>
            <div>
                <?php foreach ($bookOrderCounters['positions'] ?? [] as $position) { ?>
                    <div style="margin-bottom: 2px">
                        <?php if ($current) { ?>
                            <?= Html::a(
                                                Html::tag('i', '', ['class' => 'fa fa-edit', 'aria-hidden' => 'true']),
                                                ['book-order-position/update', 'id' => $position->id],
                                                ['class' => 'btn btn-xs btn-warning']
                                            ) ?>
                        <?php } ?>
                        <?php if ($current) { ?>
                            <?= Html::a(
                                                Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => 'true']),
                                                ['book-order-position/delete', 'id' => $position->id],
                                                ['class' => 'btn btn-xs btn-danger', 'data' => ['method' => 'POST', 'confirm' => 'Вы действительно хотите удалить эту позицию?']]
                                            ) ?>
                        <?php } ?>
                        <?= $position->book->name ?? '' ?><?= (int) $roleId !== 4 ? (' (' . ($position->office->name ?? '') . ')') : '' ?>: <?= $position->count ?> шт.
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } else { ?>
        <i>В данный момент нет открытых заказов учебников...</i>
    <?php } ?>
</div>