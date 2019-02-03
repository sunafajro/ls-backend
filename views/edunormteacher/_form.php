<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
?>

<div class="edunormteacher-form">
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

    <?= $form->field($model, 'calc_edunorm')->dropDownList($items=$norms, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_edunorm_day')->dropDownList($items=$norms, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'company')->dropDownList($items=$jobPlace, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Add'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <table class="table table-stripped table-bordered table-condensed small">
        <thead>
            <tr>
                <th><?= Yii::t('app', 'Hourly tax') ?></th>
                <th><?= Yii::t('app', 'Daily tax') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Assign date') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Job place') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Active') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Act.') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($tnorms as $tn) : ?>
            <tr>
                <td><?= $tn['nname'] ?></td>
                <td><?= $tn['ndname'] ?></td>
                <td class="text-center"><?= $tn['ndate'] ?></td>
                <td class="text-center">
                    <span class="label <?= (int)$tn['tjplace'] === 1 ? 'label-success' : 'label-info' ?>">
                        <?= $jobPlace[$tn['tjplace']] ?>
                    </span>
                </td>
                <td class="text-center"><i class="fa <?= (int)$tn['active'] === 1 ? 'fa-check' : '' ?>"></i></td>
                <td class="text-center">
                    <?= Html::a('<i class="fa fa-trash"></i>', ['edunormteacher/delete', 'id' => $tn['enid'], 'tid' => $teacher->id]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
