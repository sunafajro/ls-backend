<?php

/**
 * @var View    $this
 * @var Service $model
 * @var array   $cities
 * @var array   $eduages
 * @var array   $eduforms
 * @var array   $languages
 * @var array   $servicechanges
 * @var array   $servicetypes
 * @var array   $studnorms
 * @var array   $timenorms
 * @var string  $userInfoBlock
 */

use app\models\Service;
use app\widgets\Alert;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Update service') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Услуги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="row row-offcanvas row-offcanvas-left service-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 col-md-2 col-lg-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>При изменнии Нормы оплаты, старая стоимость фиксируется в истории.</li>
			<li>Изменение других параметров услуги не фиксируется.</li>
		</ul>
	</div>
	<div id="content" class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?= Alert::widget() ?>
		<?= $this->render('_form', [
                'model'  => $model,
                'ages'   => $eduages,
                'types'  => $servicetypes,
                'langs'  => $languages,
                'forms'  => $eduforms,
                'norms'  => $timenorms,
                'cities' => $cities,
                'costs'  => $studnorms,
		]) ?>
	</div>
    <div id="content" class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <?php foreach($servicechanges as $change) { ?>
            <div class="well small">Дата изменения: <?= $change['date'] ?><br />
                Предыдущее значение: <?= $change['value'] ?> р.<br />
                Кем изменено: <?= $change['user'] ?>
            </div>
        <?php } ?>
    </div>
</div>
