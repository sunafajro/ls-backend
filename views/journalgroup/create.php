<?php

use app\widgets\Alert;
use app\models\Journalgroup;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/**
 * @var yii\web\View $this
 * @var Journalgroup $model
 * @var array  $teachers
 * @var array  $groupInfo
 * @var array  $items
 * @var array  $params
 * @var array  $students
 * @var array  $timeHints
 * @var string $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group') . ' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add lesson');

$groupParams = [];
foreach($groupInfo as $key => $value) {
    $groupParams[] = '<span class="small"><b>' . $key . ':</b></span> <span class="text-muted small">' . $value . '</span>';
}
?>
<div class="row row-offcanvas row-offcanvas-left journalgroup-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
		<?php } ?>
        <?= $userInfoBlock ?>
        <?php if ($params['active'] == 1) { ?>
            <?php if(
                    (int)Yii::$app->session->get('user.ustatus') === 3 ||
                    (int)Yii::$app->session->get('user.ustatus') === 4 ||
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
            'students'  => $students,
            'teachers'  => $teachers,
            'timeHints' => $timeHints,
        ]) ?>
    </div>
</div>
