<?php
  use yii\helpers\Html;
  use yii\widgets\ActiveForm;
  $this->title = 'Система учета :: ' . Yii::t('app', 'Upload image');
  $this->params['breadcrumbs'][] = ['label' => \Yii::t('app','Messages'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = ['label' => $file['mname'], 'url' => ['view', 'id' => $file['mid']]];
  $this->params['breadcrumbs'][] = Yii::t('app','Upload file');
?>

<div class="row row-offcanvas row-offcanvas-left message-upload">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <div style="margin-top: 1rem">
            <h4><?= Yii::t('app', 'Hints') ?>:</h4>
            <ul>
                <li>К сообщению может быть добавлен только один файл! Если вы загрузите более, последующий перезапишет предыдущий!</li>
			    <li>Размер изображения после загрузки не изменяется, постарайтесь загружать файлы с разрешением не более 300-400px!</li>
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
        <div class="upload-form">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <?= $form->field($model, 'file')->fileInput()->label(\Yii::t('app','File')) ?>
            <div class="form-group">
                <?= Html::submitButton(\Yii::t('app','Upload'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <p>Текущий файл:</p>
        <?php if($file['mfile']!== NULL && $file['mfile']!='0') : ?>
            <?php $addr = explode('|', $file['mfile']) ?>
            <?=
            Html::img('@web/uploads/calc_message/' . $file['mid'] . '/fls/' . $addr[0],
            ['width' => '200px', 'alt' => 'Image', 'class' => 'img-thumbnail'])
            ?>
        <?php  else : ?>
            <p class="text-danger">К данному сообщению не прикреплено файлов!</p>
        <?php endif; ?>
    </div>
</div>