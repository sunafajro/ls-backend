<?php

use app\widgets\filters\FiltersWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View       $this
 * @var ActiveForm $form
 * @var array      $actionUrl
 * @var array      $hints
 * @var array      $items
 * @var array      $offices
 * @var string     $oid
 * @var array      $reportList
 * @var array      $teachers
 * @var string     $tid
 */
unset($actionUrl['start']);
unset($actionUrl['end']);
?>
<div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
    <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <div id="main-menu"></div>
    <?php } ?>	
    <?= UserInfoWidget::widget() ?>
    <?php if (!empty($reportList)) { ?>
    <div class="dropdown">
        <?= Html::button('<span class="fa fa-list-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Reports') . ' <span class="caret"></span>', ['class' => 'btn btn-default dropdown-toggle btn-sm btn-block', 'type' => 'button', 'id' => 'dropdownMenu', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'true']) ?>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
            <?php foreach ($reportList as $key => $value) { ?>
            <li><?= Html::a($key, $value, ['class'=>'dropdown-item']) ?></li>
            <?php } ?>
        </ul>            
    </div>
    <?php } ?>
    <?php
        try {
            echo FiltersWidget::widget([
                    'actionUrl' => $actionUrl,
                    'items'     => $items,
            ]);
        } catch (Exception $e) {
            echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
        }
    ?>
    <?php if (!empty($hints)) { ?>
        <h4><?= Yii::t('app', 'Подсказки') ?></h4>
        <div class="text-muted" style="margin-bottom: 20px">
            <?php foreach ($hints as $hint) { ?>
                <p><i><?= $hint ?></i></p>
            <?php } ?>
        </div>
    <?php } ?>
</div>