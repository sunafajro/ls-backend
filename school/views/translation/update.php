<?php

/**
 * @var View $this
 * @var Translation $model
 * @var array $clients
 * @var array $translators
 * @var array $languages
 * @var array $norms
 */

use school\models\Translation;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Update translation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['sidebar'] = Html::tag('ul', join('', [
    Html::tag('li', 'Положительное значение в поле Корректирровка суммы добавляется к стоимости счета.'),
    Html::tag('li', 'Отрицательное значение в поле Корректирровка суммы вычитается к стоимости счета.'),
]));

echo $this->render('_form', [
    'model' => $model,
    'languages' => $languages,
    'clients' => $clients,
    'translators' => $translators,
    'norms' => $norms,
]);
