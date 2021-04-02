<?php

/**
 * @var View $this
 * @var FileLink $model
 * @var integer $roleId
 */

use common\components\helpers\IconHelper;
use school\models\FileLink;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

if (in_array($roleId, [3, 4])) { ?>
    <h4><?= Yii::t('app', 'Actions') ?></h4>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['file-link/create'],
    ]); ?>
    <?= $form->field($model, 'file_name')->textInput() ?>
    <?= $form->field($model, 'original_name')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton(
            IconHelper::icon('upload', Yii::t('app','Create')),
            ['class' => 'btn btn-success btn-block']
        ) ?>
    </div>
    <?php ActiveForm::end();
}
