<?php

/**
 * @var View $this
 * @var Translationclient $model
 */

use school\models\Translationclient;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Update client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['translate/clients']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['sidebar'] = Html::tag('ul', join('', [
    Html::tag('li', 'Исправьте опечатки или актуализируйте информацию о клиенте и нажмите кнопку "Обновить".'),
]));

echo $this->render('_form', [
    'model' => $model,
]);
