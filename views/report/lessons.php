<?php

use app\models\Journalgroup;
use app\models\search\LessonSearch;
use app\widgets\Alert;
use app\widgets\filters\FiltersWidget;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var LessonSearch       $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var string             $actionUrl
 * @var string|null        $end
 * @var array              $offices
 * @var array              $reportList
 * @var string|null        $start
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Lessons report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Lessons report');
?>
<div class="row row-offcanvas row-offcanvas-left report-lessons">
    <?= $this->render('_sidebar', [
            'actionUrl'     => $actionUrl,
            'hints'         => [
                'Столбец Группа поддерживает фильтрацию как по названию группы так и по её номеру.',
                'Столбец Комментарии отображает только студентов присутствовавших на занятии.',
                'Статистика по студентам учитывает всех студентов отмесенных в журналах групп.'
            ],
            'items' => [
                [
                    'name'  => 'start',
                    'title' => 'Начало периода',
                    'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                    'value' => $start ?? '',
                ],
                [
                    'name'  => 'end',
                    'title' => 'Конец периода',
                    'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                    'value' => $end ?? '',
                ],
            ],
            'reportList'    => $reportList ?? [],
            'userInfoBlock' => $userInfoBlock ?? '',
    ]) ?>
    <div class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') {
            try {
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
                ]);
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php
            try {
                echo Alert::widget();
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        ?>
        <?php
            $statistics = $searchModel->getAttendanceStatistics();
        ?>
        <h3 class="text-center">
            <span style="margin-right: 10px">
                <i class="fa fa-user" aria-hidden="true"></i>
                Отметок в журнале: <?= $statistics['present'] ?? 0 ?>,
            </span>
            <span style="margin-right: 10px">
                <i class="fa fa-user-o" aria-hidden="true"></i>
                Уникальных студентов: <?= $statistics['presentReal'] ?? 0 ?>.
            </span>
        </h3>
        <?php
            try {
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout' => "{pager}\n{items}\n{pager}",
                    'columns' => [
                        'id' => [
                            'attribute' => 'id',
                            'format' => 'raw',
                            'headerOptions' => ['width' => '5%'],
                            'label' => '№',
                            'value' => function (array $model) {
                                $type = '';
                                switch ($model['type']) {
                                    case Journalgroup::TYPE_ONLINE:
                                        $type = Html::tag(
                                            'i',
                                            null,
                                            [
                                                'class' => 'fa fa-skype',
                                                'aria-hidden' => 'true',
                                                'style' => 'margin-right: 5px',
                                                'title' => Yii::t('app', 'Online lesson'),
                                            ]
                                        );
                                        break;
                                    case Journalgroup::TYPE_OFFICE:
                                        $type = Html::tag(
                                            'i',
                                            null,
                                            [
                                                'class' => 'fa fa-building',
                                                'aria-hidden' => 'true',
                                                'style' => 'margin-right: 5px',
                                                'title' => Yii::t('app', 'Office lesson'),
                                            ]
                                        );
                                        break;
                                }
                                return $model['id'] . Html::tag('br') . $type;
                            }
                        ],
                        'date' => [
                            'attribute' => 'date',
                            'format' => ['date', 'php:d.m.Y'],
                            'headerOptions' => ['width' => '5%'],
                            'label' => Yii::t('app', 'Date'),
                        ],
                        'teacherName' => [
                            'attribute' => 'teacherName',
                            'format' => 'raw',
                            'headerOptions' => ['width' => '15%'],
                            'label' => Yii::t('app', 'Teacher'),
                            'value' => function ($model) {
                                return Html::a($model['teacherName'], ['teacher/view', 'id' => $model['teacherId']]);
                            },
                        ],
                        'groupName' => [
                            'attribute' => 'groupName',
                            'format' => 'raw',
                            'headerOptions' => ['width' => '15%'],
                            'label' => Yii::t('app', 'Group'),
                            'value' => function ($model) {
                                return Html::a('№' . $model['groupId'] . ' ' . $model['groupName'], ['groupteacher/view', 'id' => $model['groupId']]);
                            },
                        ],
                        'subject' => [
                            'attribute' => 'subject',
                            'headerOptions' => ['width' => '15%'],
                            'label' => Yii::t('app', 'Subject'),
                        ],
                        'hometask' => [
                            'attribute' => 'hometask',
                            'headerOptions' => ['width' => '15%'],
                            'label' => Yii::t('app', 'Homework'),
                        ],
                        'comments' => [
                            'attribute' => 'comments',
                            'format' => 'raw',
                            'label' => Yii::t('app', 'Comments'),
                            'value' => function ($model) use ($searchModel) {
                                $commentsArr = $searchModel->getCommentsByLesson($model['id']);
                                $comments = [];
                                foreach ($commentsArr as $comment) {
                                    $comments[] = Html::tag('p',
                                        Html::a($comment['studentName'], ['studname/view', 'id' => $comment['studentId']])
                                        . Html::tag('br')
                                        . Html::tag('i', $comment['comment'] ?? '(пусто)')
                                    );
                                }
                                return implode('', $comments);
                            }
                        ],
                        'officeId' => [
                            'attribute' => 'officeId',
                            'filter' => $offices,
                            'format' => 'raw',
                            'value' => function ($model) use ($offices) {
                                return $offices[$model['officeId']] ?? $model['officeId'];
                            }
                        ]
                    ],
                ]);
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        ?>
    </div>
</div>