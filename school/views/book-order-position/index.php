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

use school\models\Book;
use school\models\BookCost;
use school\models\BookOrder;
use school\models\BookOrderPosition;
use school\models\search\BookOrderPositionSearch;
use common\widgets\alert\AlertWidget;
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
$officeId = in_array($roleId, [4]) ? (int)Yii::$app->session->get('user.uoffice_id') : null;
$bookOrderId = $bookOrder->id;
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

        <?= AlertWidget::widget() ?>

        <?php
            $columns = [
                    ['class' => 'yii\grid\SerialColumn']
            ];
            $columns['name'] = [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (array $book) {
                    return join(
                            '',
                            [
                                Html::tag('div', $book['name']),
                                Html::tag('div', $book['description'], ['class' => 'small'])
                            ]
                    );
                }
            ];
            $columns['author']      = ['attribute' => 'author'];
            $columns['isbn']        = ['attribute' => 'isbn'];
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
            $columns['purchase_cost'] = [
                'attribute' => 'purchase_cost',
                'format'    => 'raw',
                'label'     => 'Цена',
                'value'     => function (array $book) use ($roleId) {
                    $str = [
                        Html::tag(
                                'span',
                                number_format($book['paid'] ?? 0, 2, '.', ' ') . ' руб.',
                                ['title' => 'Цена продажи']
                        ),
                    ];
                    if (in_array($roleId, [3, 7])) {
                        $bookCost = BookCost::find()->andWhere(['book_id' => $book['book_id'], 'type' => BookCost::TYPE_PURCHASE, 'visible' => 1])->one();
                        $str[] = Html::tag(
                            'span',
                            '(' . number_format((($bookCost->cost ?? 0) * $book['count']), 2, '.', ' ') . ' руб.)',
                            ['title' => 'Закупочная цена']
                        );
                    }
                    return join(Html::tag('br'), $str);
                }
            ];
            if (in_array($roleId, [3, 7])) {
                $columns['officeName'] = [
                    'attribute' => 'officeName',
                    'filter'    => $offices,
                    'format'    => 'raw',
                    'value'     => function (array $book) use ($offices, $bookOrderId) {
                        $ids = explode(',',$book['officeId'] ?? '');
                        $positionsByOffices = [];
                        foreach ($ids ?? [] as $id) {
                            $id = trim($id);
                            if ($id && isset($offices[$id])) {
                                $positionsByOffices[] = $this->render('_positions_by_offices_info', [
                                    'bookId'      => $book['book_id'],
                                    'bookOrderId' => $bookOrderId,
                                    'officeId'    => $id,
                                    'offices'     => $offices,
                                ]);
                            }
                        }
                        return join('', $positionsByOffices);
                    }
                ];
            }
            if (in_array($roleId, [4])) {
                $columns['items'] = [
                    'format' => 'raw',
                    'label' => 'По студентам',
                    'value' => function(array $book) use ($bookOrderId, $officeId) {
                        /** @var BookOrderPosition $position */
                        $position = BookOrderPosition::find()->andWhere([
                            'book_id'       => $book['book_id'],
                            'book_order_id' => $bookOrderId,
                            'office_id'     => $officeId,
                            'visible'       => 1,
                        ])->one();
                        return $this->render('_position_items_by_student', [
                            'position' => $position
                        ]);
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
            }

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'layout'       => "{pager}\n{items}\n{pager}",
                'columns'      => $columns,
            ]) ?>
    </div>
</div>