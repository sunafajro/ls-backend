<?php

/**
 * @var View       $this
 * @var ActiveForm $form
 * @var array      $actionUrl
 * @var array      $filterTypes
 * @var array      $items
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<div>
    <h4><?= Yii::t('app', 'Filters') ?></h4>
    <?php
    $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to($actionUrl),
    ]);
    ?>
    <?php foreach ($items ?? [] as $item) {
        echo $this->render($filterTypes[$item['type']], [
            'item' => $item,
        ]);
    } ?>
    <div class="form-group">
        <?= Html::submitButton(
                Html::tag(
                        'i',
                        null,
                        ['class' => 'fa fa-filter', 'aria-hidden' => 'true']
                ) . Yii::t('app', 'Apply'),
                ['class' => 'btn btn-info btn-sm btn-block']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
