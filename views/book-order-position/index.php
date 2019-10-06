<?php

use app\models\Book;
use app\models\BookOrder;
use app\models\BookOrderPosition;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View              $this
 * @var Book              $book
 * @var BookOrder         $bookOrder
 * @var BookOrderPosition $model
 * @var array             $bookOrderCounters
 */

$title = Yii::t('app','Book order') . ' №' . $bookOrder->id;
$this->title = Yii::$app->params['appTitle'] . $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = $title;
?>
<div class="row row-offcanvas row-offcanvas-left book-order">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?? '' ?>
        <h4>Заказ №<?= $bookOrder->id ?></h4>
        <p><b>Дата начала:</b> <?= date('d.m.Y', strtotime($bookOrder->date_start)) ?></p>
            <p><b>Дата окончания:</b> <?= date('d.m.Y', strtotime($bookOrder->date_end)) ?></p>
            <p><b>Количество позиций:</b> <?= Html::a(
                    $bookOrder->positionsCount ?? 0,
                    ['book-order-position/index', 'id' => $bookOrder->id]
                ) ?></p>
            <p><b>Итого учебников:</b> <?= $bookOrderCounters['total_count'] ?? 0 ?></p>
            <?php if (in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) { ?>
                <p><b>Итого (закупочная цена):</b> <?= $bookOrderCounters['total_purchase_cost'] ?? 0 ?> р.</p>
            <?php } ?>
            <p><b>Итого:</b> <?= $bookOrderCounters['total_selling_cost'] ?? 0 ?> р.</p>
    </div>
    <div class="col-sm-6">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= Alert::widget() ?>

    </div>
</div>