<?php

/**
 * @var yii\web\View        $this
 * @var yii\data\Pagination $pages
 * @var array               $studentList
 * @var array               $students
 * @var float               $totalDebt
 * @var string              $name
 * @var string              $state
 * @var string              $type
 * @var string              $officeId
 */

use school\models\Office;
use school\widgets\filters\models\FilterDropDown;
use school\widgets\filters\models\FilterTextInput;
use yii\helpers\Html;

$this->title = Yii::$app->name . ' :: '.Yii::t('app','Debt report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Debt report');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl' => ['report/debt'],
        'items' => [
            new FilterTextInput([
                'name'  => 'name',
                'title' => Yii::t('app', 'Student name'),
                'value' => $name ?? '',
            ]),
            new FilterDropDown([
                'name'  => 'state',
                'title' => Yii::t('app', 'Status'),
                'options' => ['1' => 'С нами', '0' => 'Не с нами'],
                'prompt'  => Yii::t('app', '-all states-'),
                'value' => $state ?? '',
            ]),
            new FilterDropDown([
                'name'    => 'type',
                'title'   => Yii::t('app', 'Debt type'),
                'options' => ['0' => 'Нам должны', '1' => 'Мы должны'],
                'prompt'  => Yii::t('app', '-all debts-'),
                'value'   => $type ?? '',
            ]),
            new FilterDropDown([
                'name'    => 'officeId',
                'title'   => Yii::t('app', 'Offices'),
                'options' => Office::find()->select(['name'])->active()->indexBy('id')->orderBy(['name' => SORT_ASC])->column(),
                'prompt'  => Yii::t('app', '-all offices-'),
                'value'   => $officeId ?? '',
            ]),
        ],
        'hints' => [],
        'activeReport' => 'debt',
    ]
];
if ($totalDebt < 0) { ?>
    <div class="alert alert-danger" style="text-align: center">
        <b>
            Итого: <?= number_format($totalDebt, 2, '.', ' ') ?> р.
        </b>
    </div>
<?php }

$start = (int)$pages->totalCount > 0 ? 1 : 0;
$end = 20;
$nextpage = 2;
$prevpage = 0;
if (Yii::$app->request->get('page')) {
    if (Yii::$app->request->get('page')>1) {
        $start = (20 * (Yii::$app->request->get('page') - 1) + 1);
        $end = $start + 19;
        if ($end>=$pages->totalCount){
            $end = $pages->totalCount;
        }
        $prevpage = Yii::$app->request->get('page') - 1;
        $nextpage = Yii::$app->request->get('page') + 1;
    }
}
$previousPageUrl = ['report/debt','page' => $prevpage,'name' => (string)$name, 'officeId' => (string)$officeId, 'type' => (string)$type, 'state' => (string)$state];
$nextPageUrl = ['report/debt', 'page' => $nextpage,'name' => (string)$name, 'officeId' => (string)$officeId, 'type' => (string)$type, 'state' => (string)$state];
?>
<div class="row" style="margin-bottom: 0.5rem">
    <div class="col-xs-12 col-sm-3 text-left">
        <?= (($prevpage > 0) ? Html::a('Предыдущий', $previousPageUrl, ['class' => 'btn btn-default']) : '') ?>
    </div>
    <div class="col-xs-12 col-sm-6 text-center">
        <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
    </div>
    <div class="col-xs-12 col-sm-3 text-right">
        <?= (($end < $pages->totalCount) ? Html::a('Следующий', $nextPageUrl, ['class' => 'btn btn-default']) : '') ?>
    </div>
</div>
<?php foreach ($studentList as $st) { ?>
    <div class="<?= $st['debt'] >= 0 ? 'bg-success text-success' : 'bg-danger text-danger' ?>" style="padding: 15px">
        <div style="float: left"><strong><?= Html::a("#".$st['id']." ".$st['name']." →", ['studname/view', 'id'=>$st['id']]) ?></strong></div>
        <div class="text-right"><strong>(баланс: <?= number_format($st['debt'], 2, '.', ' ') ?> р.)</strong></div>
    </div>
    <table class="table table-bordered table-stripped table-hover table-condensed" style="margin-bottom: 0.5rem">
        <tbody>
        <?php foreach ($students as $s) { ?>
            <?php if ($s['stid']==$st['id']) { ?>
                <tr class="<?= $s['num'] >= 0 ? '' : 'danger'?>">
                    <td>услуга #<?= $s['sid'] ?> <?= $s['sname'] ?></td>
                    <td class="tbl-cell-10 text-right"><?= $s['num'] ?> зан.</td>
                    <td class="tbl-cell-10 text-center">
                        <?php if ($s['npd'] !== 'none') { ?>
                            <span class="label label-warning" title="Рекомендованная дата оплаты">
                                <?= $s['npd'] ?>
                            </span>
                        <?php } else { ?>
                            <span class="label label-info">Без расписания</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
<div class="row" style="margin-bottom: 0.5rem">
    <div class="col-xs-12 col-sm-3 text-left">
        <?= (($prevpage > 0) ? Html::a('Предыдущий', $previousPageUrl, ['class' => 'btn btn-default']) : '') ?>
    </div>
    <div class="col-xs-12 col-sm-6 text-center">
        <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
    </div>
    <div class="col-xs-12 col-sm-3 text-right">
        <?= (($end < $pages->totalCount) ? Html::a('Следующий', $nextPageUrl, ['class' => 'btn btn-default']) : '') ?>
    </div>
</div>