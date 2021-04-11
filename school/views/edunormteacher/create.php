<?php

/**
 * @var View $this
 * @var Edunormteacher $model
 * @var Teacher $teacher
 * @var array $norms
 * @var array $tnorms
 */

use school\models\Edunormteacher;
use school\models\Teacher;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Add tax');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['teacher/index']];
$this->params['breadcrumbs'][] = ['label' => $teacher->name, 'url' => ['teacher/view', 'id' => $teacher->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add tax');

$this->params['sidebar'] = Html::tag('ul', join('', [
    Html::tag('li', 'При добавлении новой ставки, старая помечается неактивной автоматически.'),
    Html::tag('li', 'У преподавателя может быть только одна активная ставка по одному направлению деятельности.')
]));

echo $this->render('_form', [
        'model' => $model,
        'norms' => $norms,
        'tnorms' => $tnorms,
        'teacher' => $teacher,
]);