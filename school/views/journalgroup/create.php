<?php

/**
 * @var View         $this
 * @var Groupteacher $group
 * @var Journalgroup $model
 * @var array        $teachers
 * @var array        $items
 * @var array        $params
 * @var array        $students
 * @var array        $timeHints
 * @var int          $userId
 * @var string       $userInfoBlock
 */

use school\models\Groupteacher;
use school\assets\JournalGroupFormAsset;
use common\widgets\alert\AlertWidget;
use school\models\Journalgroup;
use school\widgets\groupInfo\GroupInfoWidget;
use school\widgets\groupMenu\GroupMenuWidget;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Groups'), 'url' => ['groupteacher/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group') . ' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add lesson');

JournalGroupFormAsset::register($this);

$roleId     = (int)Yii::$app->session->get('user.ustatus');
$userId     = (int)Yii::$app->session->get('user.uid');
$teacherId  = Yii::$app->session->get('user.uteacher');
?>
<div class="row row-offcanvas row-offcanvas-left journalgroup-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
		<?php } ?>
        <?= $userInfoBlock ?>
        <?php if ($params['active'] == 1) {
            echo GroupMenuWidget::widget([
                'activeItem' => 'add-lesson',
                'canCreate'  => in_array($roleId, [3, 4]) || in_array($teacherId, array_keys($teachers)) || $userId === 296,
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

        <h4><?= Yii::t('app', 'Add lesson to journal of group') . ' #'. $params['gid'] ?></h4>
        <?= $this->render('_form', [
                'model'     => $model,
                'roleId'    => $roleId,
                'students'  => $students,
                'teachers'  => $teachers,
                'timeHints' => $timeHints,
                'userId'    => $userId,
        ]) ?>
    </div>
</div>
