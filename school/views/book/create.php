<?php

/**
 * @var View     $this
 * @var BookForm $model
 * @var array    $languages
 * @var string   $userInfoBlock
 */

use school\models\forms\BookForm;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Create book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');

$this->params['sidebar'] = '';

echo $this->render('_form', [
    'model'      => $model ?? null,
    'languages'  => $languages ?? [],
]);