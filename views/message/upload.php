<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

//$this->title = 'Upload image: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app','Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $file['mname'], 'url' => ['view', 'id' => $file['mid']]];
$this->params['breadcrumbs'][] = \Yii::t('app','Upload file');

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
    <?php if($file['mfile']!=''&&$file['mfile']!='0')
    {	
	$addr = explode('|',$file['mfile']);
        echo "<img src='uploads/calc_message/".$file['mid']."/fls/".$addr[0]."' alt='file' class='img-thumbnail'>";
    }
    else{echo "<p class='text-danger'>К данному сообщению не прикреплено файлов!</p>";}
    ?>
</div>