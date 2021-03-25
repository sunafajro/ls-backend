<?php

/**
 * @var View $this
 * @var User  $model
 * @var array $cities
 * @var array $offices
 * @var array $roles
 * @var array $teachers
 */

use school\models\User;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' .  Yii::t('app', 'Update user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['user/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');

$this->params['sidebar'] = Html::tag('ul', Html::tag('li', 'Поля Офис и Город разблокируются автоматически при выборе роли Менеджер Офиса'));

echo $this->render('_form', [
    'model' => $model,
    'teachers' => $teachers,
    'roles' => $roles,
    'offices' => $offices,
    'cities' => $cities,
]);
