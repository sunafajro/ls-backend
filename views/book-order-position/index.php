<?php

/**
 * @var View                    $this
 * @var Book                    $book
 * @var BookOrder               $bookOrder
 * @var ActiveDataProvider      $dataProvider
 * @var BookOrderPositionSearch $searchModel
 * @var array                   $bookOrderCounters
 * @var array                   $languages
 * @var array                   $offices
 */

use app\models\Book;
use app\models\BookCost;
use app\models\BookOrder;
use app\models\search\BookOrderPositionSearch;
use app\widgets\Alert;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$title = Yii::t('app','Book order') . ' №' . $bookOrder->id;
$this->title = Yii::$app->params['appTitle'] . $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = $title;
$roleId = (int)Yii::$app->session->get('user.ustatus');
$paymentTypes = $searchModel->getPaymentTypes();
?>
<div class="row row-offcanvas row-offcanvas-left book-order">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?? '' ?>
        <?= $this->render('../book-order/_order_info', [
                'current'           => false,
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
            $columns['id']          = ['attribute' => 'id'];
            $columns['name']        = ['attribute' => 'name'];
            $columns['author']      = ['attribute' => 'author'];
            $columns['isbn']        = ['attribute' => 'isbn'];
            $columns['description'] = ['attribute' => 'description'];
            $columns['publisher']   = ['attribute' => 'publisher'];
            $columns['language']    = [
                'attribute' => 'language',
                'filter'    => $languages,
                'value'     => function (array $book) use ($languages) {
                    return $languages[$book['language']] ?? '';
                }
            ];
            $columns['count']    = [
                'attribute' => 'count',
                'value' => function (array $book) {
                    return ($book['count'] ?? 0) . ' шт.';
                }
            ];
            if (in_array($roleId, [3, 7])) {
                $columns['office'] = [
                    'attribute' => 'office',
                    'filter'    => $offices,
                    'value'     => function (array $book) use ($offices) {
                        return $offices[$book['office']] ?? '';
                    }
                ];
                $columns['purchase_cost'] = [
                    'attribute' => 'purchase_cost',
                    'value'     => function (array $book) {
                        $bookCost = BookCost::find()->andWhere(['book_id' => $book['book_id'], 'type' => BookCost::TYPE_PURCHASE, 'visible' => 1])->one();
                        return number_format((($bookCost->cost ?? 0) * $book['count']), 2, '.', '') . ' руб.';
                    }
                ];
            }
            $columns['selling_cost'] = [
                'attribute' => 'selling_cost',
                'label'     => in_array($roleId, [3, 7]) ? 'Цена продажи' : 'Цена',
                'value'     => function (array $book) {
                    return ($book['paid'] ?? 0) . ' руб.';
                }
            ];
            $columns['payment_type'] = [
                'attribute' => 'payment_type',
                'filter'    => $paymentTypes,
                'format'    => 'raw',
                'value'     => function (array $book) use ($paymentTypes) {
                    $result = [
                        Html::tag('b', $paymentTypes[$book['payment_type']] ?? $book['payment_type']),
                        Html::tag('small', Html::encode($book['payment_comment'] ?? '')),
                    ];
                    return join(Html::tag('br'), $result);
                }
            ];
            $columns['actions'] = [
                'attribute' => 'actions',
                'format'    => 'raw',
                'label'     => Yii::t('app', 'Act.'),
                'value'     => function (array $book) use ($bookOrder, $roleId) {
                    $actions = [];
                    if ($bookOrder->status === BookOrder::STATUS_OPENED) {
                        if (in_array($roleId, [3, 4, 7])) {
                            $actions[] = Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-edit', 'aria-hidden' => 'true']),
                                ['book-order-position/update', 'id' => $book['id']],
                                ['title' => Yii::t('app', 'Edit book order position')]
                            );
                            $actions[] = Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => 'true']),
                                ['book-order-position/delete', 'id' => $book['id']],
                                ['data-method' => 'POST', 'title' => Yii::t('app', 'Delete book order position')]
                            );
                        }
                    }

                    return join(' ', $actions);
                }
            ];
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'layout'       => "{pager}\n{items}\n{pager}",
                'columns'      => $columns,
            ]) ?>
    </div>
</div>