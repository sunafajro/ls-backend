<?php

/**
 * @var View   $this
 * @var Receipt $model
 */

use school\models\Receipt;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Create receipt');
$this->params['breadcrumbs'][] = Yii::t('app', 'Create receipt');
$this->params['sidebar'] = join('', [
    Html::tag('h4', 'Важное напоминание!'),
    Html::tag('ul', join('', [
        Html::tag('li', 'Данный вид платежек предназначен для учеников, которых нет (и возможно не будет) в списке клиентов.'),
        Html::tag('li', 'Если на ученика есть карточка в системе будете добры создать платежку из формы которая располагается в ней.'),
    ]))
]);

$form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['receipt/download-receipt'],
    'options' => ['target' => '_blank'],
]);

echo $form->field($model, 'payer')->textInput();
echo$form->field($model, 'purpose')->textInput();
echo $form->field($model, 'sum')->Input('number', ['step' => '0.01']);
echo Html::tag('div', Html::submitButton(Yii::t('app', 'Print'), ['class' => 'btn btn-primary']));
ActiveForm::end();