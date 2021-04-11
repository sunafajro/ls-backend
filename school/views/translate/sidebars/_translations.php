<?php
/**
 * @var View $this
 * @var array $languages
 * @var array $years
 * @var array $urlParams
 */

use common\components\helpers\DateHelper;
use common\components\helpers\IconHelper;
use school\models\AccessRule;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<h4><?= Yii::t('app', 'Actions') ?>:</h4>
<div class="form-group">
    <?php if (AccessRule::checkAccess('translation_create')) { ?>
        <?= Html::a(IconHelper::icon('plus', Yii::t('app', 'Add')), ['translation/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
    <?php } ?>
    <?= Html::a(IconHelper::icon('file-text-o', Yii::t('app', 'Receipt')), ['receipt/common'], ['class' => 'btn btn-default btn-sm btn-block'] ) ?>
</div>
<?= $this->render('_menu', ['activeItem' => 'translations']) ?>
<h4><?= Yii::t('app', 'Filters') ?>:</h4>
<?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['translate/translations'],
]); ?>
    <div class="form-group">
        <input type="text" class="form-control input-sm" placeholder="Найти по наименованию..." name="TSS"<?= ($urlParams['TSS'] != NULL) ? ' value="' . $urlParams['TSS'] . '"' : '' ?>>
    </div>
    <div class="form-group">
        <select class='form-control input-sm' name='LANG'>";
            <option value='all'><?= Yii::t('app', '-all languages-') ?></option>";
            <?php foreach($languages as $key => $value){ ?>
                <option value="<?php echo $key; ?>" <?php echo ($key==$urlParams['LANG']) ? ' selected' : ''; ?>><?php echo $value; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <select class='form-control input-sm' name='MONTH'>";
            <option value='all'><?= Yii::t('app', '-all months-') ?></option>";
            <?php foreach(DateHelper::getMonths() as $key => $value){ ?>
                <option value="<?php echo $key; ?>" <?php echo ($key==$urlParams['MONTH']) ? ' selected' : ''; ?>><?php echo $value; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <select class='form-control input-sm' name='YEAR'>";
            <option value='all'><?= Yii::t('app', '-all years-') ?></option>";
            <?php foreach($years as $key => $value){ ?>
                <option value="<?php echo $key; ?>" <?php echo ($key==$urlParams['YEAR']) ? ' selected' : ''; ?>><?php echo $value; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <?= Html::submitButton(IconHelper::icon('filter', Yii::t('app', 'Apply')), ['class' => 'btn btn-info btn-sm btn-block']) ?>
    </div>
<?php ActiveForm::end(); ?>