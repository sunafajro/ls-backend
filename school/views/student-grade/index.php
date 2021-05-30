<?php

use common\components\helpers\IconHelper;
use school\assets\StudentGradeFormAsset;
use school\models\AccessRule;
use school\models\Student;
use school\models\StudentGrade;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

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

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Add attestation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view','id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add attestation');
$this->params['sidebar'] = '';

$canDownload = AccessRule::checkAccess('student-grade_download-attestation');
$canCreate = AccessRule::checkAccess('student-grade_create');
$canUpdate = AccessRule::checkAccess('student-grade_update');
$canDelete = AccessRule::checkAccess('student-grade_delete');

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
$columns[] = [
    'class' => 'yii\grid\ActionColumn',
    'header' => Yii::t('app', 'Act.'),
    'headerOptions' => ['width' => '10%'],
    'template' => '{download} {update} {delete}',
    'buttons' => [
        'download' => function ($url, $grade) {
            return Html::a(
                IconHelper::icon('print', null, Yii::t('app', 'Download')),
                ['student-grade/download-attestation', 'id' => $grade['id']],
                [
                    'class' => 'btn btn-default btn-xs',
                    'style' => 'margin-right: 0.2rem',
                    'target' => '_blank',
                ]
            );
        },
        'update' => function ($url, $grade) use ($model) {
            $mainParams = [
                ['id' => 'studentgrade-date', 'value' => $grade['date']],
                ['id' => 'studentgrade-description', 'value' => $grade['description']],
                ['id' => 'studentgrade-score', 'value' => $grade['score']],
                ['id' => 'studentgrade-teacher_id', 'value' => $grade['teacherId']],
                ['id' => 'studentgrade-office_id', 'value' => $grade['officeId']],
            ];
            $scoreContents = [];
            if ($grade['contents'] ?? false) {
                foreach (JSON::decode($grade['contents']) ?? [] as $key => $value) {
                    $name = Html::getInputName($model, 'contents') . "[{$key}]";
                    $scoreContents[] = ['name' => $name, 'value' => $value];
                }
            }
            return Html::a(
                IconHelper::icon('pencil', null, Yii::t('app', 'Update')),
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
                IconHelper::icon('trash', null, Yii::t('app', 'Delete')),
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
    'visibleButtons' => [
        'download' => $canDownload,
        'update' => $canUpdate,
        'delete' => $canDelete,
    ],
];

if ((int)$student->active === 1 && ($canCreate || $canUpdate)) { ?>
    <?= $this->render('_form', [
        'model'     => $model,
        'exams'     => $exams,
        'studentId' => $student->id,
    ]);
}
echo GridView::widget([
        'dataProvider' => $grades,
        'layout'       => "{items}\n{pager}",
        'columns'      => $columns,
]);