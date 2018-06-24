<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Salestud */

$this->title = Yii::t('app', 'Add sale');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view','id'=>$student->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row row-offcanvas row-offcanvas-left student_sale-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Допускается добавление студенту нескольких скидок.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= $this->render('_form', [
            'model' => $model,
            'sale' => $sale,
        ]) ?>
    </div>
</div>
