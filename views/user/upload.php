<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

//$this->title = 'Upload image: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = \Yii::t('app','Upload image');

?>
<div class="user-upload">
    <div class="upload-form">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'file')->fileInput()->label(\Yii::t('app','Image')) ?>

        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app','Upload'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    <p>Текущая фотография:</p>
    <?php if($user->logo!=''&&$user->logo!='0')
    {
        echo "<img src='uploads/user/".$user->id."/logo/".$user->logo."' alt='foto' class='img-thumbnail'>";
    }
    else{echo "<p class='text-danger'>У данного пользователя отсутствует фото!</p>";}
    ?>
</div>
