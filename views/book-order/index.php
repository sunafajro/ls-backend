<?php

use app\models\BookOrder;
use app\models\search\BookOrderSearch;
use app\widgets\Alert;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var BookOrder          $bookOrder
 * @var ActiveDataProvider $dataProvider
 * @var BookOrderSearch    $searchModel
 * @var array              $bookOrderCounters
 * @var string             $userInfoBlock
 */

$title = Yii::t('app','Book orders');
$this->title = Yii::$app->params['appTitle'] . $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = $title;
?>
<div class="row row-offcanvas row-offcanvas-left book-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?= $this->render('../book-order/_order_info', [
                'current'           => true,
                'bookOrder'         => $bookOrder ?? null,
                'bookOrderCounters' => $bookOrderCounters ?? [],
        ]) ?>
    </div>
    <div class="col-sm-10">
    <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= Alert::widget() ?>

        <?php
            $columns = [];
            $columns['id'] = [
                'attribute' => 'id'
            ];
            $columns['date_start'] = [
                'attribute' => 'date_start',
                'value'     => function (array $order) {
                    return date('d.m.Y', strtotime($order['date_start']));
                }
            ];
            $columns['date_end'] = [
                'attribute' => 'date_end',
                'value'     => function (array $order) {
                    return date('d.m.Y', strtotime($order['date_end']));
                }
            ];
            $columns['total_book_count'] = [
                'attribute' => 'total_book_count',
                'value'     => function (array $order) {
                    return ($order['total_book_count'] ?? 0).  ' шт.';
                }
            ];
            if (in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
                $columns['total_purchase_cost'] = [
                    'attribute' => 'total_purchase_cost',
                    'value'     => function (array $order) {
                        return ($order['total_purchase_cost'] ?? 0).  ' р.';
                    }
                ];
            }
            $columns['total_selling_cost'] = [
                'attribute' => 'total_selling_cost',
                'label'     => in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7]) ? 'Цена продажи' : 'Цена',
                'value'     => function (array $order) {
                    return ($order['total_selling_cost'] ?? 0).  ' р.';
                }
            ];
            $columns['status'] = [
                'attribute' => 'status',
                'format'    => 'raw',
                'value'     => function (array $order) {
                    return Html::tag(
                        'span',
                        BookOrder::getStatusLabel($order['status']),
                        [
                            'class' => $order['status'] === BookOrder::STATUS_OPENED
                                ? 'label label-success' : 'label label-danger',
                        ]);
                }
            ];
            if (in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
                $columns['actions'] = [
                    'attribute' => 'actions',
                    'format'    => 'raw',
                    'label'     => Yii::t('app', 'Act.'),
                    'value'     => function (array $order) use ($bookOrder) {
                        $actions = [];
                        if (empty($bookOrder) && $order['status'] === BookOrder::STATUS_CLOSED) {
                            $actions[] = Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-check', 'aria-hidden' => 'true']),
                                ['book-order/open', 'id' => $order['id']],
                                ['data-method' => 'POST', 'title' => Yii::t('app', 'Open book order')]
                            );
                        }
                        if ($order['status'] === BookOrder::STATUS_OPENED) {
                            $actions[] = Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-times', 'aria-hidden' => 'true']),
                                ['book-order/close', 'id' => $order['id']],
                                ['data-method' => 'POST', 'title' => Yii::t('app', 'Close book order')]
                            );
                            $actions[] = Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-edit', 'aria-hidden' => 'true']),
                                ['book-order/update', 'id' => $order['id']],
                                ['title' => Yii::t('app', 'Update book order')]
                            );
                        }
                        $actions[] = Html::a(
                            Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => 'true']),
                            ['book-order/delete', 'id' => $order['id']],
                            ['data-method' => 'POST', 'title' => Yii::t('app', 'Delete book order')]
                        );

                        return join(' ', $actions);
                    }
                ];
            }
        ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'layout'       => "{pager}\n{items}\n{pager}",
                'columns'      => $columns,
            ]) ?>
    </div>
</div>