<?php

use Yii;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View  $this
 * @var array $studsales
 */
?>
<p class="bg-warning" style="padding: 15px">
    <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Все добавленные в этот блок ссылки будут доступны при добавлении счета.">
        <span class="glyphicon glyphicon-info-sign"></span>
    </button>
    <b>
        <a href="#collapse-sales" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-sales" class="text-warning collapsed">
            <?= Yii::t('app', 'Temporary sales') ?>
        </a>
    </b>
</p>
<div class="collapse" id="collapse-sales" aria-expanded="false" style="height: 0px">
    <div class="panel panel-warning">
        <div class="panel-body">
            <?php foreach ($studsales as $ss) {
                $saleInfo = [];
                echo Html::beginTag('p');
                $saleInfo[] = Html::tag(
                    'p',
                    'когда и кем назначена: ' . Html::tag('br') . Html::tag('b', date('d.m.y', strtotime($ss['date']))) . ', ' . Html::tag('b', $ss['user']),
                    ['class' => 'muted small']
                );
                if (isset($ss['usedby'])) {
                    $saleInfo[] = Html::tag(
                        'p',
                        'когда и кем последний раз использована: ' . Html::tag('br') . Html::tag('b', date('d.m.y', strtotime($ss['usedate']))) . ', ' . Html::tag('b', $ss['usedby']),
                        ['class' => 'muted small']
                    );
                }
                echo Html::a(
                    Html::tag('span', null, ['class' => 'fa fa-trash']),
                    ['salestud/disable', 'id' => $ss['id']],
                    [
                        'class'        => 'btn btn-danger btn-xs',
                        'data' => [
                            'confirm' => "Вы действительно хотите аннулировать скидку {$ss['name']}?",
                            'method'  => 'post',
                        ],
                        'style'        => 'margin-right: 5px',
                        'title'        => Yii::t('app', 'Cancel'),
                    ]
                );
                echo Html::a(
                    Html::tag('span', null, ['class' => 'fa fa-info']),
                    'javascript:void(0)',
                    [
                        'class' => 'btn btn-info btn-xs',
                        'data' => [
                            'container' => 'body',
                            'content'   => join('', $saleInfo),
                            'html'      => 'true',
                            'placement' => 'top',
                            'toggle'    => 'popover',
                        ],
                        'style' => 'margin-right: 5px',
                    ]
                );
                echo $ss['name'];
                if ((int)$ss['visible'] === 1 && (int)$ss['approved'] !== 1) {
                    echo Html::tag('span', 'На проверке!', ['class' => 'label label-warning', 'style' => 'margin-left: 5px']);
                }
                echo Html::endTag('p');
            } ?>
        </div>
    </div>
</div>