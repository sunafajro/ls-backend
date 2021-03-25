<?php

/**
 * @var View $this
 * @var Translationnorm $model
 */

use school\models\Translationnorm;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Create translation pay norm');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translation pay norms'), 'url' => ['translate/norms']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');

$this->params['sidebar'] = Html::tag('ul', Html::tag('li', 'Заполните данные новой нормы оплаты и нажмите кнопку Добавить.'));

echo $this->render('_form', [
    'model' => $model,
]);

