<?php
/**
 * @var TeacherLanguagePremium $model
 * @var array                  $premiums
 * @var Teacher                $teacher
 * @var array                  $teacherPremiums
 * @var string                 $userInfoBlock
 */

use app\models\Teacher;
use app\models\TeacherLanguagePremium;
use app\widgets\alert\AlertWidget;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add language premium');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['teacher/index']];
$this->params['breadcrumbs'][] = ['label' => $teacher->name, 'url' => ['teacher/view', 'id'=>$teacher->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add language premium');
?>
<div class="row row-offcanvas row-offcanvas-left teacher-language-premium-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Выберите из выпадающего списка надбавку и нажмите кнопку добавить.</li>
			<li>У преподавателя может быть только одна надбавка на один язык.</li>
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
        <?= AlertWidget::widget() ?>
	    <?= $this->render('_form-language-premiums', [
	        'model' => $model,
	        'premiums' => $premiums
	    ]) ?>
		<hr />
		<table class="table table-stripped table-bordered table-condensed small">
			<thead>
				<tr>
					<th class="text-center"><?= Yii::t('app', 'Language') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Value') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Job place') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Assign date') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Act.') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($teacherPremiums as $tp) : ?>
				<tr>
					<td><?= $tp['language'] ?></td>
					<td class="text-center"><?= $tp['value'] ?></td>
					<td class="text-center"><?= Yii::$app->params['jobPlaces'][$tp['company']] ?></td>
					<td class="text-center"><?= $tp['created_at'] ?></td>
					<td class="text-center">
						<?= Html::a('<i class="fa fa-trash"></i>', ['teacher/delete-language-premium', 'id' => $tp['tlpid'], 'tid' => $teacher->id]) ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
        </table>
	</div>
</div>