<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Edunormteacher */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="teacherlangpremium-form">
    <?php if (Yii::$app->session->hasFlash('error')) : ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('success')) : ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>
    
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'calc_langpremium')->dropDownList($items=$premiums, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Add'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <table class="table table-stripped table-bordered table-condensed small">
        <thead>
            <tr>
                <th class="text-center"><?= Yii::t('app', 'Language') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Value') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Assign date') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Act.') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($teacher_premiums as $tp) : ?>
            <tr>
                <td><?= $tp['language'] ?></td>
                <td class="text-center"><?= $tp['value'] ?></td>
                <td class="text-center"><?= $tp['created_at'] ?></td>
                <td class="text-center">
                    <?= Html::a('<i class="fa fa-trash"></i>', ['teacherlangpremium/delete', 'id' => $tp['tlpid'], 'tid' => $teacher->id]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
