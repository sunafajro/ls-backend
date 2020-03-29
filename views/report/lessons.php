<?php

use app\models\Journalgroup;
use app\widgets\Alert;
use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this 
 * @var Journalgroup       $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var string             $actionUrl
 * @var array              $reportList
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Lessons report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Lessons report');
?>
<div class="row row-offcanvas row-offcanvas-left report-lessons">
    <?= $this->render('_sidebar', [
            'actionUrl'     => $actionUrl,
            'end'           => $end ?? '',
            'hints'         => [
                'Столбец Группа поддерживает фильтрацию как по названию группы так и по её номеру.',
                'Столбец Комментарии отображает только студентов присутствовавших на занятии.',
                'При фильтрации по столбцу Дата, фильтр по периоду игнорируется.'
            ],
            'reportList'    => $reportList ?? [],
            'start'         => $start ?? '',
            'userInfoBlock' => $userInfoBlock ?? '',
    ]) ?>
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
                    'attribute'     => 'id',
                    'format'        => 'raw',
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
                                        'class'       => 'fa fa-skype',
                                        'aria-hidden' => 'true',
                                        'style'       => 'margin-left:5px',
                                        'title'       => Yii::t('app', 'Online lesson'),
                                    ]
                                );
                                break;
                            case Journalgroup::TYPE_OFFICE:
                                $type = Html::tag(
                                    'i',
                                    null,
                                    [
                                        'class'       => 'fa fa-building',
                                        'aria-hidden' => 'true',
                                        'style'       => 'margin-left:5px',
                                        'title'       => Yii::t('app', 'Office lesson'),
                                    ]
                                );
                                break;
                        }
                        return $model['id'] . Html::tag('br') . $type;
                    }
                ],
                'date' => [
                    'attribute' => 'date',
                    'format'    => ['date', 'php:d.m.Y'],
                    'headerOptions' => ['width' => '5%'],
                    'label'     => Yii::t('app', 'Date'),
                ],
                'teacherName' => [
                    'attribute' => 'teacherName',
                    'format'    => 'raw',
                    'headerOptions' => ['width' => '15%'],
                    'label'     => Yii::t('app', 'Teacher'),
                    'value'     => function ($model) {
                        return Html::a($model['teacherName'], ['teacher/view', 'id' => $model['teacherId']]);
                    },
                ],
                'groupName' => [
                    'attribute' => 'groupName',
                    'format'    => 'raw',
                    'headerOptions' => ['width' => '15%'],
                    'label'     => Yii::t('app', 'Group'),
                    'value'     => function ($model) {
                        return Html::a('№' . $model['groupId'] . ' ' . $model['groupName'], ['groupteacher/view', 'id' => $model['groupId']]);
                    },
                ],
                'subject' => [
                    'attribute' => 'subject',
                    'headerOptions' => ['width' => '15%'],
                    'label'     => Yii::t('app', 'Subject'),
                ],
                'hometask' => [
                    'attribute' => 'hometask',
                    'headerOptions' => ['width' => '15%'],
                    'label'     => Yii::t('app', 'Homework'),
                ],
                'comments' => [
                    'attribute' => 'comments',
                    'format'    => 'raw',
                    'label'     => Yii::t('app', 'Comments'),
                    'value'     => function ($model) use ($searchModel) {
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
            ],
        ])?>
    </div>
</div>