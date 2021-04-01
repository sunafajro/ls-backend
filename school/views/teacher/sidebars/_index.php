<?php
/**
 * @var array $offices
 * @var array $languages
 * @var array $jobStates
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var Auth $auth */
$auth = Yii::$app->user->identity;
?>
<h4><?= Yii::t('app', 'Filters') ?>:</h4>
<?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['teacher/index'],
]); ?>
    <div class="form-group">
        <input type="text" class="form-control input-sm" placeholder="Найти по имени..." name="TSS" value="<?= $urlParams['TSS'] ? $urlParams['TSS'] : '' ?>">
    </div>
    <div class="form-group">
        <select class="form-control input-sm" name="STATE">
            <option value="" <?= $urlParams['STATE'] === null ? 'selected' : '' ?>><?= Yii::t('app', '-all states-') ?></option>
            <option value="0" <?= $urlParams['STATE'] === 0 ? 'selected' : '' ?>>С нами</option>
            <option value="1" <?= $urlParams['STATE'] === 1 ? 'selected' : '' ?>>Не с нами</option>
            <option value="2" <?= $urlParams['STATE'] === 2 ? 'selected' : '' ?>>В отпуске</option>
            <option value="3" <?= $urlParams['STATE'] === 3 ? 'selected' : '' ?>>В декрете</option>
        </select>
    </div>
    <div class="form-group">
        <select class='form-control input-sm' name='TOID'>";
            <option value=""><?= Yii::t('app', '-all offices-') ?></option>
            <?php foreach ($offices as $key => $value) { ?>
                <option value="<?= $key ?>"<?= (string)$key === $urlParams['TOID'] ? ' selected' : '' ?>><?= mb_substr($value,0,13,'UTF-8') ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <select class="form-control input-sm" name="TLID">
            <option value=""><?= Yii::t('app', '-all languages-') ?></option>
            <?php foreach($languages as $key => $value) { ?>
                <option value="<?= $key ?>"<?= (string)$key === $urlParams['TLID'] ? ' selected' : '' ?>><?= mb_substr($value,0,13,'UTF-8') ?></option>
            <?php } ?>
        </select>
    </div>
    <?php if ($auth->roleId === 3) { ?>
        <div class="form-group">
            <select class="form-control input-sm" name="TJID">
                <option value=""><?= Yii::t('app', '-all forms-') ?></option>
                <?php foreach($jobStates as $key => $value): ?>
                    <option value="<?= $key ?>"<?= (string)$key === $urlParams['TJID'] ? ' selected' : '' ?>><?= mb_substr($value,0,13,'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton(IconHelper::icon('filter', Yii::t('app', 'Apply')), ['class' => 'btn btn-info btn-sm btn-block']) ?>
    </div>
<?php ActiveForm::end();