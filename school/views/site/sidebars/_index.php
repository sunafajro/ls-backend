<?php
/**
 * @var View $this
 * @var ActiveForm $form
 * @var NewsSearch $searchModel
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use school\models\searches\NewsSearch;
use school\widgets\filters\FiltersWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var Auth $auth */
$auth = Yii::$app->user->identity;

if (in_array($auth->roleId, [3])) {
    echo Html::tag('h4', Yii::t('app', 'Actions') . ':');
    echo Html::a(
        IconHelper::icon('plus') . ' ' . Yii::t('app', 'News'),
        ['news/create'],
        ['class' => 'btn btn-success btn-sm btn-block']
    );
}

echo FiltersWidget::widget([
    'actionUrl' => ['site/index'],
    'items' => [
        [
            'type' => FiltersWidget::FIELD_TYPE_DATE_INPUT,
            'name' => 'NewsSearch[startDate]',
            'title' => 'Начало периода',
            'format' => 'dd.mm.yyyy',
            'value' => $searchModel->startDate,
        ],
        [
            'type' => FiltersWidget::FIELD_TYPE_DATE_INPUT,
            'name' => 'NewsSearch[endDate]',
            'title' => 'Конец периода',
            'format' => 'dd.mm.yyyy',
            'value' => $searchModel->endDate,
        ],
    ],
]);
