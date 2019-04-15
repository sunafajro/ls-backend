<?php

/**
 * @var yii\web\View             $this
 * @var yii\widgets\ActiveForm   $form
 * @var app\models\StudentGrades $model
 * @var app\models\Student       $student
 * @var array                    $contentTypes
 * @var array                    $grades
 * @var array                    $exams
 * @var string                   $userInfoBlock
 */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\grid\GridView;

$this->title = 'Система учета :: ' . Yii::t('app', 'Add attestation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view','id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add attestation');

$columns = [];
$columns[] = [
    'class' => 'yii\grid\SerialColumn',
    'header' => '№',
    'headerOptions' => ['width' => '5%'],
];
$columns[] = [
    'attribute' => 'date',
    'format' => 'raw',
    'headerOptions' => ['width' => '10%'],
    'label' => Yii::t('app', 'Date'),
    'value' => function ($grade) {
        return date('d.m.Y', strtotime($grade['date']));
    }
];
$columns[] = [
    'attribute' => 'description',
    'format' => 'raw',
    'label' => Yii::t('app', 'Description'),
    'value' => function ($grade) use ($exams) {
        return $exams[$grade['description']] ?? $grade['description'];
    }
];
$columns[] = [
    'attribute' => 'contents',
    'format' => 'raw',
    'headerOptions' => ['width' => '20%'],
    'label' => Yii::t('app', 'Exam contents'),
    'value' => function ($grade) use ($contentTypes) {
        if ($grade['contents']) {
            $contents = [];
            $json = json_decode($grade['contents']);
            foreach($json as $key => $value) {
              $contents[] = '<i>' . ($contentTypes[$key] ?? $key) . ':</i> ' . $value;
            }
            return implode('<br />', $contents);
        } else {
            return NULL;
        }
    }
];
$columns[] = [
    'attribute' => 'score',
    'format' => 'raw',
    'headerOptions' => ['width' => '10%'],
    'label' => Yii::t('app', 'Score'),
    'value' => function ($grade) {
        return $grade['score'] . ((int)$grade['type'] === 1 ? '%' : '');
    }
];
$columns[] = [
  'attribute' => 'userName',
  'format' => 'raw',
  'label' => Yii::t('app', 'Added by'),
  'value' => function ($grade) {
      return $grade['userName'];
  }
];
if (((int)Yii::$app->session->get('user.ustatus') === 3 ||
   (int)Yii::$app->session->get('user.ustatus') === 4) &&
   (int)$student->active === 1) {
    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'header' => Yii::t('app', 'Act.'),
        'headerOptions' => ['width' => '10%'],
        'template' => '{pdf}{delete}',
        'buttons' => [
            'pdf' => function ($url, $grade) {
                return Html::a(
                    Html::tag('i',
                    '',
                    [
                        'class' => 'glyphicon glyphicon-print',
                        'aria-hidden' => true,
                    ]),
                    ['student-grade/download-attestation', 'id' => $grade['id']],
                    [
                        'class' => 'btn btn-default btn-xs',
                        'style' => 'margin-right: 0.2rem',
                        'target' => '_blank',
                    ]
                );
            },
            'delete' => function ($url, $grade) {
                return Html::a(
                    Html::tag('i',
                    '',
                    [
                      'class' => 'glyphicon glyphicon-trash',
                      'aria-hidden' => true,
                    ]
                    ),
                    ['student-grade/delete', 'id' => $grade['id']],
                    [
                        'class' => 'btn btn-danger btn-xs',
                        'data' => [
                          'method' => 'post',
                        ],
                    ]
                );
            }
        ],
    ];
}
?>
<div class="row row-offcanvas row-offcanvas-left student_grade-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
	</div>
	<div id="content" class="col-sm-10">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php if (Yii::$app->session->hasFlash('error')) { ?>
		  <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
          </div>
        <?php } ?>
        <?php if (Yii::$app->session->hasFlash('success')) { ?>
		  <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success'); ?>
          </div>
        <?php } ?> 
        <?php if (
            ((int)Yii::$app->session->get('user.ustatus') === 3
            || (int)Yii::$app->session->get('user.ustatus') === 4)
            && (int)$student->active === 1
        ) { ?>
            <?= $this->render('_form', [
                'model'     => $model,
                'exams'     => $exams,
                'studentId' => $student->id,
            ]) ?>
        <?php } ?>
        <?= GridView::widget([
            'dataProvider' => $grades,
            'layout'       => "{items}\n{pager}",
            'columns'      => $columns,
        ])?>
    </div>
</div>