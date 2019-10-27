<?php

use app\models\BookCost;
use app\models\BookOrder;
use app\models\search\BookSearch;
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
 * @var BookSearch         $searchModel
 * @var array              $bookOrderCounters
 * @var array              $languages
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Books');
$this->params['breadcrumbs'][] = Yii::t('app','Books');
?>
<div class="row row-offcanvas row-offcanvas-left book-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?= Html::tag('h4', Yii::t('app', 'Actions')) ?>
        <?php if (in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
            echo Html::a(
                    Html::tag('i', '', ['class' => 'fa fa-plus', 'aria-hidden' => 'true']) .
                    ' ' .
                    Yii::t('app', 'Add'), ['book/create'],
                    ['class' => 'btn btn-success btn-sm btn-block']
            );
            if (empty($bookOrder)) {
               echo Html::a(
                    Html::tag('i', '', ['class' => 'fa fa-plus', 'aria-hidden' => 'true']) .
                    ' ' .
                    Yii::t('app', 'Open new order'), ['book-order/create'],
                    ['class' => 'btn btn-info btn-sm btn-block']
               );
            } else {
                echo Html::a(
                    Html::tag('i', '', ['class' => 'fa fa-times', 'aria-hidden' => 'true']) .
                    ' ' .
                    Yii::t('app', 'Close current order'), ['book-order/close', 'id' => $bookOrder->id],
                    ['class' => 'btn btn-danger btn-sm btn-block', 'data-method' => 'POST']
               );
            }
        }
        echo Html::a(
            Html::tag('i', '', ['class' => 'fa fa-list', 'aria-hidden' => 'true']) .
            ' ' .
            Yii::t('app', 'Order history'), ['book-order/index'],
            ['class' => 'btn btn-warning btn-sm btn-block']
        );
        echo $this->render('../book-order/_order_info', [
                'current'           => true,
                'bookOrder'         => $bookOrder ?? null,
                'bookOrderCounters' => $bookOrderCounters ?? [],
        ]);
        ?>
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
                'filter' => $languages,
                'value' => function (array $book) use ($languages) {
                    return $languages[$book['language']] ?? '';
                }
            ];

            if (in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
                $columns['purchase_cost'] = [
                    'attribute' => 'purchase_cost',
                    'value'     => function (array $book) {
                        $bookCost = BookCost::find()->andWhere(['book_id' => $book['id'], 'type' => BookCost::TYPE_PURCHASE, 'visible' => 1])->one();
                        return $bookCost->cost ?? null;
                    }
                ];
            }
            $columns['selling_cost'] = [
                'attribute' => 'selling_cost',
                'label'     => in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7]) ? 'Цена продажи' : 'Цена',
                'value'     => function (array $book) {
                    $bookCost = BookCost::find()->andWhere(['book_id' => $book['id'], 'type' => BookCost::TYPE_SELLING, 'visible' => 1])->one();
                    return $bookCost->cost ?? null;
                }
            ];
            $columns['actions'] = [
                'attribute' => 'actions',
                'format'    => 'raw',
                'label'     => Yii::t('app', 'Act.'),
                'value'     => function (array $book) use ($bookOrder) {
                    $actions = [];
                    if (!empty($bookOrder)) {
                        $actions[] = Html::a(
                            Html::tag('i', '', ['class' => 'fa fa-plus', 'aria-hidden' => 'true']),
                            ['book-order-position/create', 'order_id' => $bookOrder->id, 'book_id' => $book['id']],
                            ['title' => Yii::t('app', 'Add to the order')]
                        );
                    }
                    if (in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
                        $actions[] = Html::a(
                            Html::tag('i', '', ['class' => 'fa fa-edit', 'aria-hidden' => 'true']),
                            ['book/update', 'id' => $book['id']],
                            ['title' => Yii::t('app', 'Edit book')]
                        );
                        $actions[] = Html::a(
                            Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => 'true']),
                            ['book/delete', 'id' => $book['id']],
                            ['data-method' => 'POST', 'title' => Yii::t('app', 'Delete book')]
                        );
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
