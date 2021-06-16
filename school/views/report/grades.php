<?php

/**
 * @var yii\web\View $this
 * @var ActiveDataProvider $dataProvider
 * @var StudentGradeSearch $searchModel
 * @var string|null $start
 * @var string|null $end
 */

use school\models\searches\StudentGradeSearch;
use school\models\StudentGrade;
use school\widgets\filters\models\FilterDateInput;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Attestations');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl'     => ['report/grades'],
        'items'         => [
            new FilterDateInput([
                'name'  => 'start',
                'title' => Yii::t('app', 'Period start'),
                'format' => 'dd.mm.yyyy',
                'value' => $start ?? '',
            ]),
            new FilterDateInput([
                'name'  => 'end',
                'title' => Yii::t('app', 'Period end'),
                'format' => 'dd.mm.yyyy',
                'value' => $end ?? '',
            ]),
        ],
        'hints'         => [],
        'activeReport' => 'grades',
    ],
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'id',
        'studentName' => [
            'attribute' => 'studentName',
            'format' => 'raw',
            'label' => Yii::t('app', 'Student'),
            'value' => function (StudentGradeSearch $model) {
                return Html::a($model->studentName, ['studname/view', 'id' => $model->studentId]);
            },
        ],
        'studentBirthDate' => [
            'attribute' => 'studentBirthDate',
            'format' => ['date', 'php:d.m.Y'],
        ],
        'teacherName',
        'officeName',
        'description' => [
            'attribute' => 'description',
            'filter' => StudentGrade::getExams(),
            'format' => 'raw',
            'label' => Yii::t('app', 'Description'),
            'value' => function (StudentGradeSearch $model) {
                return StudentGrade::getExamName($model->description);
            }
        ],
        'textBook' => [
            'attribute' => 'textBook',
            'label' => Yii::t('app', 'Book'),
            'value' => function(StudentGradeSearch $model) {
                return trim($model->textBook, '"');
            }
        ],
        'date' => [
            'attribute' => 'date',
            'format' => ['date', 'php:d.m.Y'],
        ],
        'score',
    ],
]);
