<?php

/**
 * @var View               $this
 * @var GroupSearch        $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var array              $ages
 * @var array              $languages
 * @var array              $levels
 * @var array              $offices
 * @var string             $userInfoBlock
 */

use app\assets\GroupListAsset;
use app\models\Groupteacher;
use app\models\Schedule;
use app\models\search\GroupSearch;
use app\widgets\Alert;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Groups');
$this->params['breadcrumbs'][] = Yii::t('app','Groups');

GroupListAsset::register($this);

$roleId = (int)Yii::$app->session->get('user.ustatus');
?>
<div class="row row-offcanvas row-offcanvas-left group-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <ul style="margin-top: 1rem">
            <li>Информация по расписанию занятий группы, берется из раздела Расписание.</li>
            <li>В поле Услуга, при вводе числа фильтрация очуществляется по услуге, при вводе текста - по имени.</li>
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
                    'attribute'     => 'service',
                    'format'        => 'raw',
                    'headerOptions' => ['style' => 'width: 25%'],
                    'value' => function (array $group) use ($levels, $roleId) {
                        $html = [];
                        $html[] = Html::tag('div', '#' . $group['serviceId'] . ' ' . $group['service']);
                        $html[] = Html::beginTag('div', ['class' => 'js--item-name']);
                        $html[] = Html::tag('b', Yii::t('app', 'Level') . ': ');
                        $html[] = Html::tag('span', $levels[$group['level']] ?? '');
                        if (in_array($roleId, [3,4])) {
                            $html[] = Html::button(
                                Html::tag('span', '', ['class' => 'fa fa-edit', 'aria-hidden' => 'true']),
                                ['class' => 'btn btn-default btn-xs js--change-group-param', 'style' => 'margin-left: 5px']
                            );   
                        }
                        $html[] = Html::endTag('div');
                        if (in_array($roleId, [3,4])) {
                            $html[] = Html::beginTag('div', ['class' => 'input-group js--item-list', 'style' => 'display: none']);
                            $html[] = Html::beginTag('select', ['class' => 'form-control input-sm']);
                            foreach ($levels as $key => $value) {
                                $html[] = Html::tag('option', $value, ['value' => $key, 'selected' => $key == $group['level']]);
                            }
                            $html[] = Html::endTag('select');
                            $html[] = Html::tag(
                                'span',
                                Html::button(
                                    Html::tag('span', '', ['class' => 'fa fa-save', 'aria-hidden' => 'true']),
                                    [
                                        'class'     => 'btn btn-default btn-sm js--save-group-param',
                                        'data-url'  => Url::to(['groupteacher/change-params', 'id' => $group['id']]),
                                        'data-name' => 'calc_edulevel',
                                    ]
                                ),
                                ['class' => 'input-group-btn']
                            );
                            $html[] = Html::endTag('div');
                        }
                        return join('', $html);
                    }
                ],
                'age' => [
                    'attribute' => 'age',
                    'filter' => $ages,
                    'headerOptions' => ['style' => 'width: 10%'],
                    'value' => function (array $group) use ($ages) {
                        return $ages[$group['age']] ?? '';
                    }
                ],
                'language' => [
                    'attribute' => 'language',
                    'filter' => $languages,
                    'headerOptions' => ['style' => 'width: 10%'],
                    'value' => function (array $group) use ($languages) {
                        return $languages[$group['language']] ?? '';
                    }
                ],
                'schedule' => [
                    'attribute' => 'schedule',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width: 10%'],
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
                'teachers' => [
                    'attribute' => 'teachers',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width: 15%'],
                    'value' => function (array $group) {
                        return Groupteacher::getGroupTeacherListString($group['id'], Html::tag('br'), true);
                    }
                ],
                'office' => [
                    'attribute'     => 'office',
                    'format'        => 'raw',
                    'headerOptions' => ['style' => 'width: 15%'],
                    'filter'        => $offices,
                    'value'         => function (array $group) use ($offices, $roleId) {
                        $html = [];

                        $html[] = Html::beginTag('div', ['class' => 'js--item-name']);
                        $html[] = Html::tag('span', $offices[$group['office']] ?? '');
                        if (in_array($roleId, [3,4])) {
                            $html[] = Html::button(
                                Html::tag('span', '', ['class' => 'fa fa-edit', 'aria-hidden' => 'true']),
                                ['class' => 'btn btn-default btn-xs js--change-group-param', 'style' => 'margin-left: 5px']
                            );   
                        }
                        $html[] = Html::endTag('div');
                        if (in_array($roleId, [3,4])) {
                            $html[] = Html::beginTag('div', ['class' => 'input-group js--item-list', 'style' => 'display: none']);
                            $html[] = Html::beginTag('select', ['class' => 'form-control input-sm']);
                            foreach ($offices as $key => $value) {
                                $html[] = Html::tag('option', $value, ['value' => $key, 'selected' => $key == $group['office']]);
                            }
                            $html[] = Html::endTag('select');
                            $html[] = Html::tag(
                                'span',
                                Html::button(
                                    Html::tag('span', '', ['class' => 'fa fa-save', 'aria-hidden' => 'true']),
                                    [
                                        'class'     => 'btn btn-default btn-sm js--save-group-param',
                                        'data-url'  => Url::to(['groupteacher/change-params', 'id' => $group['id']]),
                                        'data-name' => 'calc_office',
                                    ]
                                ),
                                ['class' => 'input-group-btn']
                            );
                            $html[] = Html::endTag('div');
                        }
                        return join('', $html);
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
