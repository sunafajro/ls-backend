<?php

/**
 * @var View $this
 * @var Translationlang $model
 */

use school\models\Translationlang;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Update language');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Languages'), 'url' => ['translate/languages']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['sidebar'] = Html::tag('ul', Html::tag('li', 'Если вы обнаружили ошибку в названии, исправьте ее и нажмите кнопку "Обновить".'));

echo $this->render('_form', [
    'model' => $model,
]);