<?php

use school\models\Contract;
use school\models\Student;
use common\widgets\alert\AlertWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View       $this
 * @var Student    $student
 * @var Contract   $model
 * @var Contract[] $contracts
 * @var string     $userInfoBlock
 */

$this->title = 'Система учета :: ' . Yii::t('app','Client contracts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Client contracts');
?>

<div class="row row-offcanvas row-offcanvas-left langteacher-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <!--<ul>
            <li>Добавьте преподавателю те языки по которым он ведет обучение</li>
            <li>Для занятий не связанных с языками (логопед, математика) добавьте пунк «Без привзки к языку»</li>
        </ul>-->
    </div>
    <div id="content" class="col-sm-6">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= AlertWidget::widget() ?>
        <?= $this->render('_form', [
            'model' => $model
        ]) ?>
        <hr />
        <table class="table table-stripped table-bordered table-condensed small">
			<thead>
				<tr>
					<th class="text-center"><?= Yii::t('app', 'Contract number') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Contract date') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Contract signer') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Act.') ?></th>
				</tr>
			</thead>
			<tbody>
                <?php foreach($contracts as $c) { ?>
                    <tr>
                        <td><?= $c->number ?? '' ?></td>
                        <td class="text-center"><?= date('d.m.y', strtotime($c->date)) ?></td>
                        <td class="text-center"><?= $c->signer ?? '' ?></td>
                        <td class="text-center">
                            <?= Html::a('<i class="fa fa-trash"></i>', ['contract/delete', 'id' => $c->id]) ?>
                        </td>
                    </tr>
                <?php } ?>
			</tbody>
        </table>
	</div>
</div>
