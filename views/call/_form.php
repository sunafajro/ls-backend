<?php

/**
 * @var View  $this
 * @var Call  $model
 * @var array $age
 * @var array $eduform
 * @var array $language
 * @var array $level
 * @var array $office
 * @var array $servicetype
 * @var array $way
 */

use app\assets\CallFormAsset;
use app\models\Call;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\widgets\autocomplete\AutoCompleteWidget;

CallFormAsset::register($this);

$sex = [1=>Yii::t('app','Male'), 2=>Yii::t('app','Female')];
?>

<div class="calc-call-form" style="padding-bottom: 140px">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'phone')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'calc_sex')->dropDownList($sex,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_way')->dropDownList($way,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_servicetype')->dropDownList($servicetype,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_lang')->dropDownList($language,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_edulevel')->dropDownList($level,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_eduage')->dropDownList($age,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?php
        // при редактировании запроса на обучение выводим список типов обучения и офисов
        if(!$model->isNewRecord && $model->calc_servicetype == 1) {
			echo "<div id='hidden-field'>";
            echo $form->field($model, 'calc_eduform')->dropDownList($eduform,['prompt'=>Yii::t('app', '-select-')]);
			echo $form->field($model, 'calc_office')->dropDownList($office,['prompt'=>Yii::t('app', '-select-')]);
			echo "</div>";
        }
		// для остальных случаях списки выводим в скрытом виде
        else {
			echo "<div id='hidden-field' style='display: none'>";
            echo $form->field($model, 'calc_eduform')->dropDownList($eduform,['prompt'=>Yii::t('app', '-select-')]);
			echo $form->field($model, 'calc_office')->dropDownList($office,['prompt'=>Yii::t('app', '-select-')]);
			echo "</div>";
        }
    ?>    
  
    <?= AutoCompleteWidget::widget([
        'hiddenField' => [
            'name' => Html::getInputName($model, 'calc_studname'),
        ],
        'searchField' => [
            'label' => Yii::t('app','Link to Client'),
            'url' => Url::to(['call/autocomplete']),
            'minLength' => 1,
            'error' => NULL,
        ],
    ]) ?>
    
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
