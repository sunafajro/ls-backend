<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View  $this
 * @var array $services
 * @var int   $studentId
 */
?>
<p class="bg-info" style="padding: 15px">
    <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Если у студента доступных занятий меньше или равно 0, проверить занятия на которых он присутствовал будет нельзя."><span class="glyphicon glyphicon-info-sign"></span></button>
    <strong><a href="#collapse-lessons" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapse-lessons" class="text-info"><?= Yii::t('app', 'Payed lessons count') ?></a></strong>
</p>
<div class="collapse in" id="collapse-lessons" aria-expanded="true">
    <div class="panel panel-info">
        <div class="panel-body">
            <span class="muted small">
                <?php foreach ($services as $service) {
                    echo Html::a(
                        Html::tag('i', '', ['class' => 'fa fa-eye-slash', 'aria-hidden' => 'true']),
                        ['studname/update-settings', 'id' => $studentId],
                        [
                            'style' => 'margin-right: 5px',
                            'title' => 'Скрыть из карточки клиента',
                            'data' => [
                                'method'  => 'post',
                                'params' => ['name' => 'serviceId', 'value' => $service['sid'], 'action' => 'hide']
                            ],
                        ]
                    );
                    echo "услуга " . Html::tag('b',"#{$service['sid']}") . " {$service['sname']} - осталось " . Html::tag('b', $service['num']) . " занятий.";
                    if ($service['npd'] !== 'none') {
                        echo Html::tag(
                            'span',
                            date('d.m.Y', strtotime($service['npd'])),
                            ['class' => 'label label-warning', 'title' => 'Рекомендованная дата оплаты', 'style' => 'margin-left: 5px']
                        );
                    } else {
                        echo Html::tag('span', 'Без расписания', ['class' => 'label label-info', 'style' => 'margin-left: 5px']);
                    }
                    echo Html::tag('br');
                ?>
                <?php } ?>
            </span>
        </div>
    </div>
</div>