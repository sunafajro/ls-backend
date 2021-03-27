<?php
/**
 * @var View $this
 * @var array $languages
 * @var array $urlParams
 * @var bool $canCreate
 */

use common\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<?php if ($canCreate) { ?>
    <h4><?= Yii::t('app', 'Actions') ?>:</h4>
    <div class="form-group">
        <?= Html::a(IconHelper::icon('plus', Yii::t('app', 'Add')), ['translator/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
    </div>
<?php } ?>
<?= $this->render('_menu', ['activeItem' => 'translators']) ?>
<h4><?= Yii::t('app', 'Filters') ?>:</h4>
<?php
$form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['translate/translators'],
]);
?>
    <div class="form-group">
        <input type="text" class="form-control input-sm" placeholder="Найти по имени..." name="TSS"<?= ($urlParams['TSS'] != NULL) ? ' value="' . $urlParams['TSS'] . '"' : '' ?>>
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
        <select name="NOTAR" class="form-control input-sm">
            <option value="all">-нот.завер.-</option>
            <option value="1"<?= ($urlParams['NOTAR'] == '1') ? ' selected' : '' ?>>Да</option>
            <option value="0"<?= ($urlParams['NOTAR'] == '0') ? ' selected' : '' ?>>Нет</option>
        </select>
    </div>
    <div class="form-group">
        <?= Html::submitButton(IconHelper::icon('filter', Yii::t('app', 'Apply')), ['class' => 'btn btn-info btn-sm btn-block']) ?>
    </div>
<?php ActiveForm::end(); ?>
