<?php
/**
 * @var View $this
 * @var BookOrder $bookOrder
 * @var array $bookOrderCounters
 * @var int $roleId
 */
use common\components\helpers\IconHelper;
use school\models\BookOrder;
use yii\helpers\Html;
use yii\web\View;

echo Html::a('Учет по офисам', ['office-book/index'], ['class' => 'btn btn-sm btn-primary btn-block']);
echo Html::tag('h4', Yii::t('app', 'Actions'));
if (in_array($roleId, [3, 7])) {
    echo Html::a(
        IconHelper::icon('plus', Yii::t('app', 'Add')),
        ['book/create'],
        ['class' => 'btn btn-success btn-sm btn-block']
    );
    if (empty($bookOrder)) {
        echo Html::a(
            IconHelper::icon('plus', Yii::t('app', 'Open new order')),
            ['book-order/create'],
            ['class' => 'btn btn-info btn-sm btn-block']
        );
    } else {
        echo Html::a(
            IconHelper::icon('times', Yii::t('app', 'Close current order')),
            ['book-order/close', 'id' => $bookOrder->id],
            ['class' => 'btn btn-danger btn-sm btn-block', 'data-method' => 'POST']
        );
    }
}
echo Html::a(
    IconHelper::icon('list', Yii::t('app', 'Order history')),
    ['book-order/index'],
    ['class' => 'btn btn-warning btn-sm btn-block']
);
echo $this->render('//book-order/_order_info', [
    'current'           => true,
    'bookOrder'         => $bookOrder ?? null,
    'bookOrderCounters' => $bookOrderCounters ?? [],
]);
