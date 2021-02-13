<?php

/**
 * @var View  $this
 * @var array $data
 */

use yii\helpers\Html;
use yii\web\View;

$jobPlaces = Yii::$app->params['jobPlaces'] ?? [];

$groupParams = [];
if (!empty($data)) {
    $groupParams[] = Html::tag('b', Yii::t('app', 'Service')      . ':') . ' ' . $data['serviceName'] ?? '';
    $groupParams[] = Html::tag('b', Yii::t('app', 'Level')        . ':') . ' ' . $data['levelName']   ?? '';
    $groupParams[] = Html::tag('b', Yii::t('app', 'Teacher')      . ':') . ' ' . $data['teachers']    ?? '';
    $groupParams[] = Html::tag('b', Yii::t('app', 'Office')       . ':') . ' ' . $data['officeName']  ?? '';
    $groupParams[] = Html::tag('b', Yii::t('app', 'Start date')   . ':') . ' ' . Html::tag('span', date('d.m.Y', strtotime($data['date'])), ['class' => 'label label-default']);
    $groupParams[] = Html::tag('b', Yii::t('app', 'Books')        . ':') . ' ' . $data['books'] ?? '';
    if ($data['active']) {
        $groupParams[] = Html::tag('b', Yii::t('app', 'Status')   . ':') . ' ' . Html::tag('span', Yii::t('app', 'Active'), ['class' => 'label label-success']);
        $groupParams[] = Html::tag('b', Yii::t('app', 'Schedule') . ':') . ' ' . $data['schedule'] ?? '';
    } else {
        $groupParams[] = Html::tag('b', Yii::t('app', 'State')    . ':') . ' ' . Html::tag('span',  Yii::t('app', 'Finished'), ['class' => 'label label-danger']);
    }
    $groupParams[] = Html::tag('b', Yii::t('app', 'Duration'). ':') . ' ' . $data['duration'] . ' ч.';
    $groupParams[] = Html::tag('b', Yii::t('app', 'Job place'). ':') . ' ' . Html::tag('span', $jobPlaces[$data['company']] ?? '', ['class' => 'label label-' . ((int)$data['company'] === 1 ? 'success' : 'info')]);
}
?>
<div>
    <h4>Параметры группы №<?= $data['id'] ?? '-' ?></h4>
    <div class="well">
        <?= join(Html::tag('br'), $groupParams) ?>
    </div>
</div>