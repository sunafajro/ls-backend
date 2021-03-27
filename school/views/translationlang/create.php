<?php

/**
 * @var View $this
 * @var Translationlang $model
 */

use school\models\Translationlang;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Система учета :: ' . Yii::t('app', 'Add language');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Languages'), 'url' => ['translate/languages']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');

$this->params['sidebar'] = Html::tag('ul', Html::tag('li', 'Укажите название языка и нажмите кнопку "Добавить".'));

echo $this->render('_form', [
    'model' => $model,
]);
