<?php

/**
 * @var yii\web\View        $this
 * @var app\models\Salestud $model
 * @var app\models\Student  $student
 * @var array               $sales
 * @var string              $userInfoBlock
 */

use app\models\Sale;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add sale');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view','id'=>$student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add sale');
?>

<div class="row row-offcanvas row-offcanvas-left student_sale-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <div id="main-menu"></div>
        <?php } ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Допускается добавление студенту нескольких скидок.</li>
		</ul>
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
        <?= $this->render('_form', [
            'model' => $model,
            'studentId' => $student->id,
        ]) ?>
        <h4>Назначенные скидки:</h4>
        <table class="table table-striped table-bordered table-hover table-condensed small">
            <thead>
                <tr>
                    <td><?= Yii::t('app', 'Sale') ?></td>
                    <td><?= Yii::t('app', 'Value') ?></td>
                    <td>Дата назначения</td>
                    <td>Кем назначено</td>
                    <td><?= Yii::t('app', 'Act.') ?></td>
                </tr>
            </thead>
            <tbody>
            <?php foreach($sales as $sale): ?>
                <tr class="<?= (int)$sale['visible'] === 0 ? 'danger' : '' ?>">
                    <td><?= $sale['name'] ?></td>
                    <td><?= $sale['value'] ?><?= (int)$sale['type'] === Sale::TYPE_RUB ? ' руб.' : '%' ?></td>
                    <td><?= $sale['date'] ?></td>
                    <td><?= $sale['user'] ?></td>
                    <td>
                        <?php if ((int)$sale['visible'] === 0) { ?>
                            <?= Html::a('', ['salestud/enable', 'id' => $sale['id']], 
                                [
                                    'class'=>'fa fa-check', 
                                    'title'=>Yii::t('app','Enable'),
                                ]) 
                            ?>
                        <?php } ?>
                        <?php if ((int)$sale['visible'] === 1) { ?>
                            <?= Html::a('', ['salestud/disable', 'id' => $sale['id']], 
                                [
                                    'class'=>'fa fa-times', 
                                    'title'=>Yii::t('app','Disable'),
                                ]) 
                            ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
