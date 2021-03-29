<?php

/**
 * @var View  $this
 * @var array $item
 */

use yii\helpers\Html;
use yii\web\View;
use kartik\datetime\DateTimePicker;
?>
<div class="form-group">
    <label><?= $item['title'] ?? '' ?>:</label>
    <?php
    try {
        echo DateTimePicker::widget([
            'name' => $item['name'] ?? '',
            'options' => [
                'autocomplete' => 'off',
                'class'        => 'form-control ' . join(' ', $item['addClasses'] ?? []),
            ],
            'pluginOptions' => [
                'language'       => 'ru',
                'format'         => $item['format'] ?: 'yyyy-mm-dd',
                'todayHighlight' => true,
                'minView'        => 2,
                'maxView'        => 4,
                'weekStart'      => 1,
                'autoclose'      => true,
            ],
            'type' => DateTimePicker::TYPE_INPUT,
            'value' => $item['value'] ?? '',
        ]);
    } catch (Exception $e) {
        echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
    } ?>
</div>
