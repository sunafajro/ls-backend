<?php

/**
 * @var app\models\Journalgroup     $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var yii\web\View                $this
 * @var array                       $reportlist
 * @var string                      $userInfoBlock
 */

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\Breadcrumbs;
use yii\grid\GridView;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Lessons report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Lessons report');
?>
<div class="row row-offcanvas row-offcanvas-left report-lessons">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>	
        <?= $userInfoBlock ?>
        <?php if(!empty($reportlist)) { ?>
            <div class="dropdown">
			    <?= Html::button('<span class="fa fa-list-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Reports') . ' <span class="caret"></span>', ['class' => 'btn btn-default dropdown-toggle btn-sm btn-block', 'type' => 'button', 'id' => 'dropdownMenu', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'true']) ?>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                    <?php foreach($reportlist as $key => $value) { ?>
                        <li><?= Html::a($key, $value, ['class'=>'dropdown-item']) ?></li>
                    <?php } ?>
			    </ul>            
		    </div>
        <?php } ?>
        <ul style="margin-top: 1rem">
            <li>Столбец Группа поддерживает фильтрацию как по названию группы так и по её номеру.</li>
            <li>Столбец Комментарии отображает только студентов присутствовавших на занятии.</li>
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
                    'headerOptions' => ['width' => '5%'],
                    'label' => '№',
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