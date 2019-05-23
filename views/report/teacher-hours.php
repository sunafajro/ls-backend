<?php

/**
 * @var yii\web\View $this
 * @var string       $end
 * @var array        $hours
 * @var array        $reportList
 * @var string       $start
 * @var array        $teachers
 * @var string       $tid
 * @var string       $userInfoBlock
 */

use Yii;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Teacher hours');
?>
<div class="row row-offcanvas row-offcanvas-left report-teacher-hours">
    <?= $this->render('_sidebar', [
        'actionUrl'     => ['report/teacher-hours'],
        'end'           => $end,
        'reportList'    => $reportList,
        'teachers'      => $teachers,
        'tid'           => $tid,
        'start'         => $start,
        'userInfoBlock' => $userInfoBlock,
    ]) ?>
    <div class="col-xs-12 col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= Alert::widget() ?>
        <table class="table table-striped table-bordered table-hover table-condensed small">
            <thead>
                <th style="width: 5%">№</th>
                <th style="width: 50%"><?= Yii::t('app', 'Teacher') ?></th>
                <th style="width: 15%"><?= Yii::t('app', 'Hours') ?></th>
                <th style="width: 15%"><?= Yii::t('app', 'Students') ?></th>
                <th style="width: 15%"><?= Yii::t('app', 'Human/hour') ?></th>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($hours as $key => $row) { ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= Html::a($row['name'],['teacher/view', 'id' => $key]) ?></td>
                        <td><?= $row['hours'] ?></td>
                        <td><?= $row['students'] ?></td>
                        <td><?= number_format($row['students'] / $row['hours'], 1, '.', ' ') ?></td>
                    </tr>
                    <?php $i++; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>