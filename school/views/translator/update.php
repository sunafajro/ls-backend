<?php

/**
 * @var View $this
 * @var Translator $model
 */

use school\models\Translator;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Update translator');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translators'), 'url' => ['translate/translators']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['sidebar'] = Html::tag('ul', join('', [
    Html::tag('li', 'Укажите данные нового переводчика.'),
    Html::tag('li', 'В поле Сайт, можно указать адрес профиля социальной сети или другой ресурс.'),
]));

echo $this->render('_form', [
'model' => $model,
]);