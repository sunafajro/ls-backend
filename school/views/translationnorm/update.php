<?php

/**
 * @var View $this
 * @var Translationnorm $model
 */

use school\models\Translationnorm;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Update translation pay norm');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translator/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translation pay norms'), 'url' => ['translate/norms']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['sidebar'] = Html::tag('ul', Html::tag('li', 'Вы можете исправить название или тип нормы оплаты, но не ее значение!'));

echo $this->render('_form', [
'model' => $model,
]);