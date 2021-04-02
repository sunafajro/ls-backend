<?php

/**
 * @var View               $this
 * @var BookOrder          $bookOrder
 * @var ActiveDataProvider $dataProvider
 * @var BookSearch         $searchModel
 * @var array              $bookOrderCounters
 * @var array              $languages
 * @var string             $userInfoBlock
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use school\models\BookCost;
use school\models\BookOrder;
use school\models\searches\BookSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Books');
$this->params['breadcrumbs'][] = Yii::t('app','Books');

/** @var Auth $auth */
$auth = \Yii::$app->user->identity;
$this->params['sidebar'] = ['bookOrder' => $bookOrder, 'bookOrderCounters' => $bookOrderCounters, 'roleId' => $auth->roleId];

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

if (in_array($auth->roleId, [3, 7])) {
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
    'label'     => in_array($auth->roleId, [3, 7]) ? 'Цена продажи' : 'Цена',
    'value'     => function (array $book) {
        $bookCost = BookCost::find()->andWhere(['book_id' => $book['id'], 'type' => BookCost::TYPE_SELLING, 'visible' => 1])->one();
        return $bookCost->cost ?? null;
    }
];
$columns['actions'] = [
    'attribute' => 'actions',
    'format'    => 'raw',
    'label'     => Yii::t('app', 'Act.'),
    'value'     => function (array $book) use ($bookOrder, $auth) {
        $actions = [];
        $bookCost = BookCost::find()->andWhere(['book_id' => $book['id'], 'type' => BookCost::TYPE_PURCHASE, 'visible' => 1])->one();
        if (!empty($bookOrder) && !empty($bookCost)) {
            $actions[] = Html::a(
                IconHelper::icon('plus'),
                ['book-order-position/create', 'order_id' => $bookOrder->id, 'book_id' => $book['id']],
                ['title' => Yii::t('app', 'Add to the order')]
            );
        }
        if (in_array($auth->roleId, [3, 7])) {
            $actions[] = Html::a(
                IconHelper::icon('edit'),
                ['book/update', 'id' => $book['id']],
                ['title' => Yii::t('app', 'Edit book')]
            );
            $actions[] = Html::a(
                IconHelper::icon('trash'),
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
]);
