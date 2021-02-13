<?php
/**
 * @var array $accrualDates
 * @var int   $roleId
 * @var array $teacherData
 */

use yii\helpers\Html;
?>
<?php foreach ($accrualDates as $key => $accrualDate) { ?>
    <div style="margin-bottom:1rem">
        <a href="#collapse-teacheraccruals-<?= $key ?>" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-teacheraccruals-<?= $key ?>" class="text-warning">
            Начисление от <?= date('d.m.y', strtotime($accrualDate)) ?>
        </a>
        <div class="collapse" id="collapse-teacheraccruals-<?= $key ?>">
            <?php $total = 0; ?>
            <?php foreach ($teacherData as $accrual) { ?>
                <?php if ($accrual['date'] === $accrualDate) { ?>
                    <div class="panel panel-default" style="margin-bottom:0.5rem">
                        <div class="panel-body small">
                            Начисление зарплаты #<?= $accrual['aid'] ?> за <?= $accrual['hours'] ?> ч. в группе #<?= $accrual['gid'] ?> (<?= trim($accrual['serviceName']) ?>,
                            <?= Html::tag('span', (int)$accrual['groupCompany'] === 1 ? 'ШИЯ' : 'СРР', ['class' => (int)$accrual['groupCompany'] === 1 ? 'label label-success' : 'label label-info']) ?>
                            ) ставка <?= $accrual['tax'] ?> р. на сумму <?= round($accrual['value']) ?> р.
                            <?php if ($roleId === 3 || $roleId === 8) { ?>
                                <?php if (!$accrual['done']) {
                                    echo " " . Html::a('', ['accrual/done', 'id' => $accrual['aid'], 'type'=>'profile'], ['class'=>'glyphicon glyphicon-ok', 'title'=>'Выплатить начисление', 'data-method' => 'post']);
                                    if ($roleId === 3) {
                                        echo " " . Html::a('', ['accrual/delete', 'id' => $accrual['aid']], ['class'=>'glyphicon glyphicon-trash', 'title'=>'Отменить начисление', 'data-method' => 'post']);
                                    }
                                } else {
                                    if ($roleId === 3) {
                                        echo " " . Html::a('', ['accrual/undone', 'id' => $accrual['aid']], ['class'=>'glyphicon glyphicon-remove', 'title'=>'Отменить выплату', 'data-method' => 'post']);
                                    }
                                }
                            } ?>
                            <?php $total = $total + $accrual['value']; ?>
                            <div>Начислено: <?= date('d.m.Y', strtotime($accrual['create_date'])) ?>, <?= $accrual['creator'] ?></div>
                            <?php if ($accrual['done']) { ?>
                                Выплачено: <?= date('d.m.Y', strtotime($accrual['finish_date'])) ?>, <?= $accrual['finisher'] ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div><b>Всего:</b> <?= round($total) ?> р.</div>
    </div>
<?php } ?>
