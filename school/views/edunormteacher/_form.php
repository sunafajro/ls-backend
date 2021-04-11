<?php

/**
 * @var View $this
 * @var Edunormteacher $model
 * @var Teacher $teacher
 * @var array $norms
 * @var array $tnorms
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use school\models\Edunormteacher;
use school\models\Teacher;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$jobPlaces = Yii::$app->params['jobPlaces'] ?? [];
?>
<div class="edunormteacher-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'calc_edunorm')->dropDownList($items=$norms, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_edunorm_day')->dropDownList($items=$norms, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'company')->dropDownList($jobPlaces, ['prompt'=>Yii::t('app', '-select-')]) ?>

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
        <?php foreach ($tnorms as $tn) { ?>
            <tr>
                <td><?= $tn['nname'] ?></td>
                <td><?= $tn['ndname'] ?></td>
                <td class="text-center"><?= $tn['ndate'] ?></td>
                <td class="text-center">
                    <span class="label <?= (int)$tn['tjplace'] === 1 ? 'label-success' : 'label-info' ?>">
                        <?= $jobPlaces[$tn['tjplace']] ?>
                    </span>
                </td>
                <td class="text-center"><?= (int)$tn['active'] === 1 ? IconHelper::icon('check') : null ?></td>
                <td class="text-center">
                    <?php
                        if (AccessRule::checkAccess('edunormteacher_delete')) {
                            echo Html::a(IconHelper::icon('trash'), ['edunormteacher/delete', 'id' => $tn['enid'], 'tid' => $teacher->id]);
                        }
                    ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
