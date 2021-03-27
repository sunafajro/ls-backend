<?php

/**
 * @var View $this
 * @var Translationclient $model
 */

use school\models\Translationclient;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Create client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['translate/clients']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['sidebar'] = Html::tag('ul', join('', [
    Html::tag('li', 'Заполните указанные поля и нажмите кнопку "Создать".'),
]));

echo $this->render('_form', [
    'model' => $model,
]);
