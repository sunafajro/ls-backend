<?php

/**
 * @var View $this
 * @var AccessRule $model
 */

use school\models\AccessRule;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Create access rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Access rules'), 'url' => ['admin/access-rules']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['sidebar'] = '';
echo $this->render('_form', [
    'model' => $model,
]);