<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcPhonebook */

$this->title = 'Система учета :: '.Yii::t('app','Update phone');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Students'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id'=>$student->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update phone');
?>
<div class="row row-offcanvas row-offcanvas-left student_phone-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Допускается добавление студенту нескольких телефонов.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
    <?= $this->render('_form', [
        'model' => $model,
        'phones'=>$phones,
    ]) ?>
    </div>
</div>

