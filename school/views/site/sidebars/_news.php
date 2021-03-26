<?php
/**
 * @var View $this
 * @var ActiveForm $form
 * @var integer $roleId
 * @var array $urlParams
 */
?>
<?php use common\components\helpers\DateHelper;
use common\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

if (in_array($roleId, [3])) { ?>
    <h4><?= Yii::t('app', 'Actions') ?>:</h4>
    <?= Html::a(
        IconHelper::icon('plus') . ' ' . Yii::t('app', 'News'),
        ['news/create'],
        ['class' => 'btn btn-success btn-sm btn-block']
    ) ?>
<?php } ?>
<h4><?= Yii::t('app', 'Filters') ?>:</h4>
<?php
$form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['site/index'],
]);
?>
<div class="form-group">
    <select class="form-control input-sm" name="month">
        <option value="all"><?= Yii::t('app', '-all months-') ?></option>
        <?php foreach(DateHelper::getMonths() as $key => $value): ?>
            <option	value="<?= $key ?>"<?= ($key==$urlParams['month']) ? ' selected' : '' ?>><?= $value ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <select class="form-control input-sm" name="year">
        <?php for($i = date('Y'); $i >= \Yii::$app->params['startYear']; $i--) { ?>
            <option value="<?= $i ?>"<?= ($i==$urlParams['year']) ? ' selected' : '' ?>><?= $i ?></option>
        <?php } ?>
    </select>
</div>
<div class="form-group">
    <?= Html::submitButton(
        IconHelper::icon('filter') . ' ' . Yii::t('app', 'Apply'),
        ['class' => 'btn btn-info btn-sm btn-block']
    ) ?>
</div>
<?php ActiveForm::end(); ?>
