<?php
/**
 * @var View $this
 * @var array $offices
 * @var string|int $state
 * @var string|int $oid
 * @var string|int $tss
 * @var int $roleId
 */

use common\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

if (in_array($roleId, [3, 4])) { ?>
    <h4><?= Yii::t('app', 'Actions') ?>:</h4>
    <?= Html::a(
        IconHelper::icon('file-text-o', Yii::t('app', 'Receipt')),
        ['receipt/common'],
        ['class' => 'btn btn-default btn-sm btn-block']
    ) ?>
<?php } ?>
<h4><?= Yii::t('app', 'Filters') ?>:</h4>
<?php
$form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['studname/index'],
]);
?>
<div class="form-group">
    <input type="text" class="form-control input-sm" placeholder="имя или телефон..." name="TSS" value="<?= $tss != '' ? $tss : '' ?>">
</div>
<div class="form-group">
    <select class="form-control input-sm" name="STATE">
        <option value='all'><?= Yii::t('app','-all states-') ?></option>
        <option value="1"<?= (int)$state === 1 ? 'selected' : '' ?>>С нами</option>
        <option value="2"<?= (int)$state === 2 ? 'selected' : '' ?>>Не с нами</option>
    </select>
</div>
<div class="form-group">
    <select class="form-control input-sm" name="OID">
        <option value='all'><?= Yii::t('app','-all offices-') ?></option>
        <?php foreach($offices as $key => $value) { ?>
            <option value="<?= $key ?>"<?= (int)$oid === (int)$key ? 'selected' : '' ?>><?= $value ?></option>
        <?php } ?>
    </select>
</div>
<div class="form-group">
    <?= Html::submitButton(IconHelper::icon('filter', Yii::t('app', 'Apply')), ['class' => 'btn btn-info btn-sm btn-block']) ?>
</div>
<?php ActiveForm::end();
