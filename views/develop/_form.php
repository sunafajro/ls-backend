<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use moonland\tinymce\TinyMCE;
/* @var $this yii\web\View */
/* @var $model app\models\Develop */
/* @var $form yii\widgets\ActiveForm */
$types = ['1'=>Yii::t('app','New features'), '2'=>Yii::t('app','Bug fixes'), '3'=>Yii::t('app','Minor changes')];
$levels = ['1'=>Yii::t('app','High'), '2'=>Yii::t('app','Medium'), '3'=>Yii::t('app','Low')];
?>

<div class="develop-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList($items=$types) ?>

    <?= $form->field($model, 'description')->widget(TinyMCE::className(), [
            'toggle' => [
                'active' => false,
            ]
        ]);
    ?>

    <?= $form->field($model, 'severity')->dropDownList($items=$levels) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
