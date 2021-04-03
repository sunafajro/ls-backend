<?php

/**
 * @var View       $this
 * @var ActiveForm $form
 * @var array      $actionUrl
 * @var string     $activeReport
 * @var string     $additionalInfo
 * @var array      $hints
 * @var array      $items
 * @var array      $offices
 * @var string     $oid
 * @var array      $reportList
 * @var array      $teachers
 * @var string     $tid
 */

use common\components\helpers\AlertHelper;
use common\components\helpers\IconHelper;
use school\models\Report;
use school\widgets\filters\FiltersWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

unset($actionUrl['start']);
unset($actionUrl['end']);

$reportList = Report::getReportTypes();
if (!empty($reportList)) { ?>
    <div class="dropdown">
        <?= Html::button(
                IconHelper::icon('list-alt', Yii::t('app', 'Reports') . ' ' . Html::tag('span', '', ['class' => 'caret'])),
                [
                    'class' => 'btn btn-default dropdown-toggle btn-sm btn-block',
                    'type' => 'button',
                    'id' => 'dropdownMenu',
                    'data-toggle' => 'dropdown',
                    'aria-haspopup' => 'true',
                    'aria-expanded' => 'true',
                ]) ?>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
            <?php foreach ($reportList as $reportType) { ?>
            <li class="<?= $reportType['id'] === $activeReport ? ' active' : '' ?>">
                <?= Html::a($reportType['label'], $reportType['url'], ['class'=>'dropdown-item']) ?>
            </li>
            <?php } ?>
        </ul>
    </div>
<?php }
try {
    echo FiltersWidget::widget([
            'actionUrl' => $actionUrl,
            'items'     => $items,
    ]);
} catch (Exception $e) {
    echo AlertHelper::alert($e->getMessage());
}
if (!empty($hints)) { ?>
    <h4><?= Yii::t('app', 'Подсказки') ?></h4>
    <div class="text-muted" style="margin-bottom: 20px">
        <?php foreach ($hints as $hint) { ?>
            <p><i><?= $hint ?></i></p>
        <?php } ?>
    </div>
<?php }
echo $additionalInfo ?? '';
