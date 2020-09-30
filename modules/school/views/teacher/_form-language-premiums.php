<?php
/**
 * @var app\models\TeacherLanguagePremiums $model
 * @var array                              $premiums
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="teacherlangpremium-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'language_premium_id')->dropDownList($items = $premiums, ['prompt' => Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'company')->dropDownList($items = Yii::$app->params['jobPlaces'], ['prompt' => Yii::t('app', '-select-')]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Add'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
