<?php

/**
 * @var View $this
 * @var News $model
 */

use school\models\News;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Update news');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','News'), 'url' => ['site/index']];
$this->params['breadcrumbs'][] = ['label' => $model->subject];
$this->params['breadcrumbs'][] = Yii::t('app','Update');

$this->params['sidebar'] = Html::tag('ul', Html::tag('li', 'Кратко опишите нововведения в системе.'));
echo $this->render('_form', [
    'model' => $model,
]);
