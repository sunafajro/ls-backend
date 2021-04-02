<?php

/**
 * @var View       $this
 * @var User       $user
 * @var UploadForm $imageForm
 */

use common\components\helpers\IconHelper;
use school\models\forms\UploadForm;
use school\models\User;
use common\widgets\alert\AlertWidget;
use school\models\UserImage;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<div>
    <?php
        $form = ActiveForm::begin([
            'action' => ['user/upload-image', 'id' => $user->id],
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
    ?>
    <?= $form->field($imageForm, 'file')->fileInput(['accept' => UserImage::getAllowedMimes()])->label(Yii::t('app','Image'))->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton(IconHelper::icon('picture-o', Yii::t('app', 'Change')), ['class' => 'btn btn-success btn-xs']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>