<?php

/**
 * @var View $this
 * @var UploadForm $uploadForm
 * @var integer $roleId
 */

use common\components\helpers\IconHelper;
use school\models\forms\UploadForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

if (in_array($roleId, [3, 4])) { ?>
    <h4><?= Yii::t('app', 'Actions') ?></h4>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['document/upload'],
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>
    <?= $form->field($uploadForm, 'file')->fileInput()->label(Yii::t('app','File')) ?>
    <div class="form-group">
        <?= Html::submitButton(
            IconHelper::icon('upload') . ' ' . Yii::t('app','Upload'),
            ['class' => 'btn btn-success btn-block']
        ) ?>
    </div>
    <?php ActiveForm::end();
}
