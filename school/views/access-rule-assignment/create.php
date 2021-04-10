<?php

/**
 * @var View $this
 * @var AccessRuleAssignment $model
 */

use school\models\AccessRuleAssignment;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Create access rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Access rule assignments'), 'url' => ['admin/access-rule-assignments']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['sidebar'] = '';
echo $this->render('_form', [
    'model' => $model,
]);