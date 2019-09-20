<?php

use app\models\Teacher;
use app\models\Schedule;
use app\models\search\GroupSearch;
use app\widgets\Alert;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var GroupSearch        $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var array              $offices
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Groups');
$this->params['breadcrumbs'][] = Yii::t('app','Groups');
?>
<div class="row row-offcanvas row-offcanvas-left group-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <ul style="margin-top: 1rem">
            <li>Информация по расписанию занятий группы, берется из раздела Расписание.</li>
        </ul>
    </div>
    <div class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= Alert::widget() ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => "{pager}\n{items}\n{pager}",
            'columns'      => [
                'id' => [
                    'attribute' => 'id',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width: 5%'],
                    'value'     => function (array $group) {
                        return Html::a($group['id'], ['groupteacher/view', 'id' => $group['id']]);
                    }
                ],
                'service' => [
                    'attribute' => 'service',
                    'headerOptions' => ['style' => 'width: 25%'],
                ],
                'teachers' => [
                    'attribute' => 'teachers',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width: 20%'],
                    'value' => function (array $group) {
                        $teacherTable = Teacher::tableName();
                        $teachers = (new \yii\db\Query())
                                ->select(['name' => 't.name'])
                                ->from('calc_teachergroup tg')
                                ->innerJoin("$teacherTable t", "t.id = tg.calc_teacher")
                                ->where([
                                    'tg.visible' => 1,
                                    'tg.calc_groupteacher' => $group['id'],
                                ])
                                ->orderBy(['t.name' => SORT_ASC])
                                ->column() ?? [];
                        return join(Html::tag('br'), $teachers);
                    }
                ],
                'schedule' => [
                    'attribute' => 'schedule',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width: 20%'],
                    'value' => function (array $group) {
                        $scheduleTable = Schedule::tableName();
                        $schedule = (new \yii\db\Query())
                            ->select([
                                'dayName' => 'd.name',
                                'timeStart' => 's.time_begin',
                                'timeEnd' => "s.time_end"
                            ])
                            ->from("$scheduleTable s")
                            ->innerJoin("calc_denned d", "d.id = s.calc_denned")
                            ->where([
                                's.calc_groupteacher' => $group['id'],
                                's.visible' => 1
                            ])
                            ->orderBy(['s.calc_denned' => SORT_ASC, 's.time_begin' => SORT_ASC])
                            ->all() ?? [];
                        if (empty($schedule)) {
                            return '';
                        } else {
                            $schedule = ArrayHelper::index($schedule, null, 'dayName');
                            $result = [];
                            foreach ($schedule as $day => $lessons) {
                                if (!empty($lessons)) {
                                    $result[] = Html::tag('b', $day);
                                    foreach ($lessons as $lesson) {
                                        $result[] = Html::tag(
                                            'i',
                                            substr($lesson['timeStart'], 0, 5) . '-' . substr($lesson['timeEnd'], 0, 5));
                                    }
                                }
                            }
                            return join(Html::tag('br'), $result);
                        }
                    }
                ],
                'office' => [
                    'attribute' => 'office',
                    'headerOptions' => ['style' => 'width: 20%'],
                    'filter' => $offices,
                    'value'     => function (array $group) use ($offices) {
                        return $offices[$group['office']] ?? '';
                    }
                ],
                'visible' => [
                    'attribute' => 'visible',
                    'filter' => [
                        0 => Yii::t('app', 'Finished'),
                        1 => Yii::t('app', 'Active')
                    ],
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width: 10%'],
                    'value'     => function (array $group) {
                        return $group['visible'] ?
                            Html::tag(
                                'span',
                                Yii::t('app', 'Active'),
                                ['class' => 'label label-success']
                            ) :
                            Html::tag(
                                'span',
                                Yii::t('app', 'Finished'),
                                ['class' => 'label label-danger']
                            );
                    }
                ],
            ]
        ]) ?>
    </div>
</div>
