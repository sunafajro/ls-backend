<?php

use yii\web\View;

/**
 * @var View  $this
 * @var array $student
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Create invoice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student['name'], 'url' => ['studname/view','id' => $student['id']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create invoice');
?>
<div id="app-invoice" data-sid="<?= $student['id'] ?>" class="row">
	<div class="col-sm-12">
		<div class="alert alert-warning"><b>Подождите.</b> Загружаем форму создания счета...</div>
	</div>
</div>
<?= $this->registerJsFile('/js/invoices/bundle.js',  ['position' => yii\web\View::POS_END]) ?>
