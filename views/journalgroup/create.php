<?php

/**
 * @var View         $this
 * @var Journalgroup $model
 * @var array        $teachers
 * @var array        $groupInfo
 * @var array        $items
 * @var array        $params
 * @var int          $roleId
 * @var array        $students
 * @var array        $timeHints
 * @var int          $userId
 * @var string       $userInfoBlock
 */

use app\widgets\Alert;
use app\models\Journalgroup;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group') . ' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add lesson');

$groupParams = [];
foreach($groupInfo as $key => $value) {
    $groupParams[] = Html::tag('span', Html::tag('b', $key . ':'), ['class' => 'small']) . ' ' . Html::tag('span', $value, ['class' => 'text-muted small']);
}
?>
<div class="row row-offcanvas row-offcanvas-left journalgroup-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
		<?php } ?>
        <?= $userInfoBlock ?>
        <?php if ($params['active'] == 1) { ?>
            <?php if (in_array($roleId, [3, 4]) ||
                    (int)Yii::$app->session->get('user.uid') === 296 ||
                    array_key_exists(Yii::$app->session->get('user.uteacher'), $teachers)) { ?>
                <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> '.Yii::t('app','Add lesson'), ['journalgroup/create','gid' => $params['gid']], ['class' => 'btn btn-block btn-primary']) ?>
            <?php } ?>
            <?php foreach($items as $item) { ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php } ?>
        <?php } ?> 
        <h4>Параметры группы №<?= $params['gid']; ?></h4>
		<div class="well well-sm"><?= join('<br />', $groupParams) ?></div>
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
        
        <?= Alert::widget() ?>

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
