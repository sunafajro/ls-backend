<?php

/** @var View        $this
 * @var Groupteacher $model
 * @var ActiveForm   $form
 * @var array        $levels
 * @var array        $offices
 * @var array        $services
 */

use app\models\Groupteacher;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
$jobPlace = Yii::$app->params['jobPlaces'];
?>
<div class="group-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'calc_service')->dropDownList($services, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'calc_edulevel')->dropDownList($levels, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'calc_office')->dropDownList($offices, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'company')->dropDownList($jobPlace, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
