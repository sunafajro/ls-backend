<?php

use app\models\BookOrder;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View       $this
 * @var BookOrder  $model
 * @var ActiveForm $form
 */
?>
<div class="book-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date_start')->widget(DateTimePicker::class, [
          'pluginOptions' => [
              'language' => 'ru',
                  'format' => 'yyyy-mm-dd',
                  'todayHighlight' => true,
                  'minView' => 2,
                  'maxView' => 4,
                  'weekStart' => 1,
                  'autoclose' => true,
          ]
      ]);
    ?>

    <?= $form->field($model, 'date_end')->widget(DateTimePicker::class, [
          'pluginOptions' => [
              'language' => 'ru',
                  'format' => 'yyyy-mm-dd',
                  'todayHighlight' => true,
                  'minView' => 2,
                  'maxView' => 4,
                  'weekStart' => 1,
                  'autoclose' => true,
          ]
      ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app','Update'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>