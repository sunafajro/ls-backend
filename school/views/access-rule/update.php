<?php

/**
 * @var View $this
 * @var Role $model
 */

use school\models\Role;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Update access rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['admin/access-rules']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->params['sidebar'] = '';
echo $this->render('_form', [
    'model' => $model,
]);