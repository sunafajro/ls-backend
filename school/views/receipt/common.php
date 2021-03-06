<?php

use common\widgets\alert\AlertWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

/**
 * @var View   $this
 * @var string $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Receipt');
$this->params['breadcrumbs'][] = Yii::t('app', 'Create receipt');
$roleId = Yii::$app->session->get('user.ustatus');
?>
<div class="row row-offcanvas row-offcanvas-left receipt-common">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <h4>Важное напоминание!</h4>
        <ul>
            <li>Данный вид платежек предназначен для учеников, которых нет (и возможно не будет) в списке клиентов.</li>
            <li>Если на ученика есть карточка в системе будете добры создать платежку из формы которая располагается в ней.</li>
		</ul>
    </div>
    <div class="col-sm-6">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
                ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= AlertWidget::widget() ?>

        <?php
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['receipt/download-receipt'],
                'options' => ['target' => '_blank'],
            ]);
        ?>
            <?= $form->field($model, 'payer')->textInput() ?>
            <?= $form->field($model, 'purpose')->textInput() ?>
            <?= $form->field($model, 'sum')->textInput() ?>
            <div class="group-form">
                <?= Html::submitButton(Yii::t('app', 'Print'), ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>