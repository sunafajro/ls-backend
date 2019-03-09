<?php
  use yii\helpers\Html;
  $this->title = 'Система учета :: ' . Yii::t('app', 'Messages');
  $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Messages'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = Yii::t('app', 'Add message');
?>

<div class="row row-offcanvas row-offcanvas-left message-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <div style="margin-top: 1rem">
            <h4><?= Yii::t('app', 'Hints') ?>:</h4>
            <ul>
                <li>После создания сообщения, оно не будет отправлено автоматически, это необходимо сделать вручную!</li>
			    <li>Вы можете прикрепить файл только после создания сообщения, но до его отправки!</li>
		    </ul>
        </div>
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
        
        <?php if(Yii::$app->session->hasFlash('error')) : ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php endif; ?>
   
        <?php if(Yii::$app->session->hasFlash('success')) : ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php endif; ?>
        
        <?= $this->render('_form', [
            'model' => $model,
	        'types' => $types,
        ]) ?>
    </div>
</div>
