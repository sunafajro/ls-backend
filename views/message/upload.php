<?php
  use yii\helpers\Html;
  use yii\widgets\ActiveForm;
  $this->title = 'Система учета :: ' . Yii::t('app', 'Upload image');
  $this->params['breadcrumbs'][] = ['label' => \Yii::t('app','Messages'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = ['label' => $file['mname'], 'url' => ['view', 'id' => $file['mid']]];
  $this->params['breadcrumbs'][] = Yii::t('app','Upload file');

?>
<div class="user-upload">
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