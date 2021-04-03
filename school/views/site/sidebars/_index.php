<?php
/**
 * @var View $this
 * @var ActiveForm $form
 * @var NewsSearch $searchModel
 */

use common\components\helpers\AlertHelper;
use common\components\helpers\IconHelper;
use school\models\Auth;
use school\models\searches\NewsSearch;
use school\widgets\filters\FiltersWidget;
use school\widgets\filters\models\FilterDateInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var Auth $auth */
$auth = Yii::$app->user->identity;

if (in_array($auth->roleId, [3])) {
    echo Html::tag('h4', Yii::t('app', 'Actions') . ':');
    echo Html::a(
        IconHelper::icon('plus', Yii::t('app', 'News')),
        ['news/create'],
        ['class' => 'btn btn-success btn-sm btn-block']
    );
}
try {
    echo FiltersWidget::widget([
        'actionUrl' => ['site/index'],
        'items' => [
            new FilterDateInput([
                'name' => 'NewsSearch[startDate]',
                'title' => Yii::t('app', 'Period start'),
                'format' => 'dd.mm.yyyy',
                'value' => $searchModel->startDate ?? '',
            ]),
            new FilterDateInput([
                'name' => 'NewsSearch[endDate]',
                'title' => Yii::t('app', 'Period end'),
                'format' => 'dd.mm.yyyy',
                'value' => $searchModel->endDate ?? '',
            ]),
        ],
    ]);
} catch (Exception $e) {
    echo AlertHelper::alert($e->getMessage());
}