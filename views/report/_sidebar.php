<?php

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/**
 * @var View       $this
 * @var ActiveForm $form
 * @var array      $actionUrl
 * @var string     $end
 * @var array      $hints
 * @var array      $offices
 * @var string     $oid
 * @var array      $reportList
 * @var string     $start
 * @var array      $teachers
 * @var string     $tid
 * @var string     $userInfoBlock
 */
unset($actionUrl['start']);
unset($actionUrl['end']);
?>
<div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
    <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <div id="main-menu"></div>
    <?php } ?>	
    <?= $userInfoBlock ?>
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
    <h4><?= Yii::t('app', 'Filters') ?></h4>
    <?php 
        $form = ActiveForm::begin([
            'method' => 'get',
            'action' => Url::to($actionUrl),
        ]);
    ?>
    <div class="form-group">
        <b>Начало периода:</b>
        <?= DateTimePicker::widget([
            'name' => 'start',
            'options' => [
                'autocomplete' => 'off',
            ],
            'pluginOptions' => [
                'language' => 'ru',
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'minView' => 2,
                'maxView' => 4,
                'weekStart' => 1,
                'autoclose' => true,
            ],
            'type' => DateTimePicker::TYPE_INPUT,
            'value' => $start,
        ]);?>
    </div>
    <div class="form-group">
        <b>Конец периода:</b>
        <?= DateTimePicker::widget([
            'name' => 'end',
            'options' => [
                'autocomplete' => 'off',
            ],
            'pluginOptions' => [
                'language' => 'ru',
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'minView' => 2,
                'maxView' => 4,
                'weekStart' => 1,
                'autoclose' => true,
                'autocomplete' => 'off',
            ],
            'type' => DateTimePicker::TYPE_INPUT,
            'value' => $end,
        ]);?>
    </div>
    <?php if (!empty($offices)) { ?>
        <?php if ((int)Yii::$app->session->get('user.ustatus') === 3) { ?>
            <div class="form-group">
                <select name="oid" class="form-control input-sm">
                    <option value><?= Yii::t('app', '-all offices-') ?></option>
                    <?php foreach ($offices as $key => $value) { ?>
                        <option value="<?= $key ?>" <?= (int)$oid === (int)$key ? 'selected' : ''?>>
                            <?= mb_substr($value, 0, 16) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (!empty($teachers)) { ?>
        <div class="form-group">
            <select name="tid" class="form-control input-sm">
                <option value><?= Yii::t('app', '-all teachers-') ?></option>
                <?php foreach ($teachers as $key => $value) { ?>
                    <option value="<?= $key ?>" <?= (int)$tid === (int)$key ? 'selected' : ''?>>
                        <?= mb_substr($value, 0, 16) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <?php if (!empty($hints)) { ?>
        <h4><?= Yii::t('app', 'Подсказки') ?></h4>
        <div class="text-muted" style="margin-bottom: 20px">
            <?php foreach ($hints as $hint) { ?>
                <p><i><?= $hint ?></i></p>
            <?php } ?>
        </div>
    <?php } ?>
</div>