<?php
/**
 * @var View $this
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<h4><?= Yii::t('app', 'Actions') ?>:</h4>
<div class="form-group">
    <?= Html::a(IconHelper::icon('plus', Yii::t('app', 'Add')), ['translationnorm/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
</div>
<?= $this->render('_menu', ['activeItem' => 'payNorms']) ?>
<h4><?= Yii::t('app', 'Filters') ?>:</h4>
<?php
$form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['translate/norms'],
]);
?>
<div class="form-group">
    <input type="text" class="form-control input-sm" placeholder="Найти по имени..." name="TSS"<?= ($urlParams['TSS'] != NULL) ? ' value="' . $urlParams['TSS'] . '"' : '' ?>>
</div>
<div class="form-group">
    <?= Html::submitButton(IconHelper::icon('filter', Yii::t('app', 'Apply')), ['class' => 'btn btn-info btn-sm btn-block']) ?>
</div>
<?php ActiveForm::end(); ?>