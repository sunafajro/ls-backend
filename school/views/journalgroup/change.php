<?php

/**
 * @var View       $this
 * @var ActiveForm $form
 * @var Groupteacher $group
 * @var array      $checkTeachers
 * @var array      $dates
 * @var array      $history
 * @var array      $items
 * @var array      $params
 * @var array      $students
 * @var string     $userInfoBlock
 */

use school\models\Groupteacher;
use school\assets\JournalGroupFormAsset;
use common\widgets\alert\AlertWidget;
use school\widgets\groupInfo\GroupInfoWidget;
use school\widgets\groupMenu\GroupMenuWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Edit lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Groups'), 'url' => ['groupteacher/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group').' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit lesson');

JournalGroupFormAsset::register($this);

$roleId    = (int)Yii::$app->session->get('user.ustatus');
$userId    = (int)Yii::$app->user->identity->id;
$teacherId = (int)Yii::$app->session->get('user.uteacher');
?>
<div class="row row-offcanvas row-offcanvas-left journalgroup-change">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
	    <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
		<?php } ?>
		<?= $userInfoBlock ?>
		<?php if($params['active'] == 1) {
            echo GroupMenuWidget::widget([
                'activeItem' => 'add-lesson',
                'canCreate'  => in_array($roleId, [3, 4]) || in_array($teacherId, array_keys($checkTeachers)) || $userId === 296,
                'groupId'    => $group->id,
            ]);
        } ?>
        <?= GroupInfoWidget::widget(['group' => $group]) ?>
	</div>
	<div class="col-sm-10">
	    <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
			<?= Breadcrumbs::widget([
				'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
			]); ?>
		<?php } ?>
	    <p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?= AlertWidget::widget() ?>
		<h4><?= Yii::t('app', 'Change lesson members') . ' #' . $params['gid'] ?></h4>
		<hr>
		<p>
			<strong>дата добавления состава:</strong> <?= !empty($students) ? date('d.m.Y', strtotime($students[0]['date'])) : '' ?><br>
			<strong>кто добавил состав:</strong> <?= !empty($students) ? $students[0]['user'] : '' ?>
		</p>
    	<?php $form = ActiveForm::begin(); ?>

    <?php
	if (!empty($students)) {
        echo $this->render('_attendance', [
            'students'  => $students,
            'isNew'     => false,
            'successes' => $params['successes'],
        ]);
		echo Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']);
	}
    ActiveForm::end(); ?>
    <h4><?= Yii::t('app', 'Lesson members history') . ' #' . $params['gid'] ?></h4>
    <hr>
	<?php foreach ($dates as $key => $value) { ?>
		<strong><?= date('d.m.Y', strtotime($value)) ?></strong>
		<p>
		<?php foreach($history as $h) { ?>
			<?php if ($h['timestamp']==$key) { ?>
				<?= $h['sname'] ?>: <em><small><?= $h['stname'] ?></small></em><br>
			<?php } ?>
		<?php } ?>
		</p>
	<?php } ?>
   </div>
</div>
