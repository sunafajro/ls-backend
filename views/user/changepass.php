<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\widgets\Breadcrumbs;
    $this->title = 'Система учета :: ' . Yii::t('app','Change password').': '.$model->name;
    $this->params['breadcrumbs'][] = ['label' => \Yii::t('app','Users'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = \Yii::t('app','Change password');
?>

<div class="row row-offcanvas row-offcanvas-left user-changepass">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <ul>
			<li>Укажите и сохраните новый пароль</li>
		</ul>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php if (Yii::$app->session->getFlash('success')) : ?>
            <div class='alert alert-success' role='alert'>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>
        <?php if(Yii::$app->session->getFlash('error')): ?>
            <div class='alert alert-danger' role='alert'>
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'pass')->passwordInput() ?>
        <?= $form->field($model, 'pass_repeat')->passwordInput()->label(\Yii::t('app','Password repeat')) ?>
         <div class="form-group">
            <?= Html::submitButton(\Yii::t('app','Update'), ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>