<?php

/**
 * @var View $this
 * @var EducationLevel $model
 */

use school\models\EducationLevel;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Update education level');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Education levels'), 'url' => ['admin/education-levels']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['sidebar'] = '';
echo  $this->render('_form', [
    'model' => $model,
]);