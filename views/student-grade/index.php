<?php

use app\assets\StudentGradeFormAsset;
use app\models\Student;
use app\models\StudentGrade;
use app\widgets\Alert;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

/**
 * @var View         $this
 * @var ActiveForm   $form
 * @var StudentGrade $model
 * @var Student      $student
 * @var array        $contentTypes
 * @var array        $grades
 * @var array        $exams
 * @var string       $userInfoBlock
 */

StudentGradeFormAsset::register($this);

$this->title = 'Система учета :: ' . Yii::t('app', 'Add attestation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view','id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add attestation');

$roleId = (int)Yii::$app->session->get('user.ustatus');

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
        if ($grade['contents'] ?? false) {
            $contents = [];
            $json = JSON::decode($grade['contents']);
            foreach($json ?? [] as $key => $value) {
              $contents[] = Html::tag('i', ($contentTypes[$key] ?? $key)  . ':') . ' ' . $value;
            }
            return implode(Html::tag('br'), $contents);
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
        'template' => '{pdf}{edit}{delete}',
        'buttons' => [
            'pdf' => function ($url, $grade) {
                return Html::a(
                    Html::tag('i',
                    '',
                    [
                        'class' => 'fa fa-print',
                        'aria-hidden' => 'true',
                    ]),
                    ['student-grade/download-attestation', 'id' => $grade['id']],
                    [
                        'class' => 'btn btn-default btn-xs',
                        'style' => 'margin-right: 0.2rem',
                        'target' => '_blank',
                    ]
                );
            },
            'edit' => function ($url, $grade) use ($model) {
                $mainParams = [
                    ['id' => 'studentgrade-date', 'value' => $grade['date']],
                    ['id' => 'studentgrade-description', 'value' => $grade['description']],
                    ['id' => 'studentgrade-score', 'value' => $grade['score']], 
                ];
                $scoreContents = [];
                if ($grade['contents'] ?? false) {
                    foreach (JSON::decode($grade['contents']) as $key => $value) {
                        $name = Html::getInputName($model, 'contents') . "[{$key}]";
                        $scoreContents[] = ['name' => $name, 'value' => $value];
                    }
                }
                return Html::a(
                    Html::tag(
                        'i',
                        '',
                        [
                            'class' => 'fa fa-pencil',
                            'aria-hidden' => 'true',
                        ]
                    ),
                    'javascript:void(0)',
                    [
                        'class' => 'btn btn-xs btn-warning js--edit-attestation',
                        'style' => 'margin-top: 0.2rem;margin-right: 0.2rem',
                        'data' => [
                            'action-url' => Url::to(['student-grade/update', 'id' => $grade['id']]),
                            'main-params' => JSON::encode($mainParams),
                            'score-contents' => JSON::encode($scoreContents),
                        ],
                    ]
                );
            },
            'delete' => function ($url, $grade) {
                return Html::a(
                    Html::tag('i',
                    '',
                    [
                      'class' => 'fa fa-trash',
                      'aria-hidden' => 'true',
                    ]
                    ),
                    ['student-grade/delete', 'id' => $grade['id']],
                    [
                        'class' => 'btn btn-danger btn-xs',
                        'style' => 'margin-top: 0.2rem',
                        'data' => [
                            'method'  => 'post',
                            'confirm' => 'Действительно удалить эту аттестацию?',
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
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
		<?= $userInfoBlock ?>
	</div>
	<div id="content" class="col-sm-10">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= Alert::widget() ?>
        <?php if (in_array($roleId, [3, 4]) && (int)$student->active === 1) { ?>
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
