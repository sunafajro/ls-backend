<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Moneystud */

$this->title = Yii::t('app', 'Create payment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id'=>$student->id, 'tab'=>4]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row row-offcanvas row-offcanvas-left payment-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Оплата помечается "остаточной", если необходимо погасить счет, по которому школа должна студенту отработать занятия, а он уже их ранее оплатил.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= $this->render('_form', [
            'model' => $model,
            'offices'=>$offices,
        ]) ?>
    </div>
</div>
