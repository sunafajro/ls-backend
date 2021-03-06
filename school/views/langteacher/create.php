<?php

/**
 * @var yii\web\View $this
 * @var array        $langs
 * @var array        $tlangs
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

$this->title = 'Система учета :: ' . Yii::t('app','Teacher languages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['teacher/index']];
$this->params['breadcrumbs'][] = ['label' => $teacher['tname'], 'url' => ['teacher/view','id'=>$teacher['tid']]];
$this->params['breadcrumbs'][] = Yii::t('app','Teacher languages');

//составляем список преподавателей для селекта
$langs = ArrayHelper::map($slangs ?? [], 'lid', 'lname');
foreach ($tlangs as $tlang) {
    while (array_search($tlang['lname'], $langs)) {
       $key = array_search($tlang['lname'], $langs);
       unset($langs[$key]);
    }
}
?>

<div class="row row-offcanvas row-offcanvas-left langteacher-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <ul>
            <li>Добавьте преподавателю те языки по которым он ведет обучение</li>
            <li>Для занятий не связанных с языками (логопед, математика) добавьте пункт «Без привзки к языку»</li>
        </ul>
    </div>
    <div id="content" class="col-sm-6">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php if ($tlangs != 0) : ?>
            <h4>Список языков преподавателя « <?= $teacher['tname'] ?> »</h4>
            <hr>
            <?php foreach ($tlangs as $tlang) : ?>
            <p>
            <strong><?= $tlang['lname'] ?></strong> 
            <?= Html::a("<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>",['langteacher/disable','id' => $tlang['clid'], 'tid' => $teacher['tid']]) ?>
            <br>кем добавлен: <?= $tlang['uname'] ?>
            <br>когда добавлен: <?= $tlang['cldate'] ?>
            </p>
        <?php endforeach; ?>
        <?php endif; ?>
        <h4>Добавление языка преподавателю « <?= $teacher['tname'] ?> »</h4>
        <hr>
        <?= $this->render('_form', [
            'model' => $model,
            'langs' => $langs,
            'teacher' => $teacher,
        ]) ?>
	</div>
</div>
