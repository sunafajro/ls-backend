<?php

use app\models\Journalgroup;
use app\widgets\Alert;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

/**
 * @var yii\web\View $this
 * @var Journalgroup $model
 * @var string $userInfoBlock
 * @var array $teachers
 * @var array $groupInfo
 * @var array $items
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Update lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group').' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lesson').' №' . $model->id];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update lesson');

$script = <<< JS
function updateEndTime(time) {
    if (typeof time === 'string' && time.length === 5) {
        var startTime = time.split(':');
        var endHour = parseInt(startTime[0], 10) + 1;
        endHour = endHour < 10 ? ('0' + endHour) : endHour;
        $("#js--lesson-end-time").val(endHour + ':' + startTime[1]);        
    }
}
$("#js--lesson-start-time").on('change', function (e) {
    updateEndTime(e.target.value);
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);

$groupParams = [];
foreach($groupInfo as $key => $value) {
    $groupParams[] = '<span class="small"><b>' . $key . ':</b></span> <span class="text-muted small">' . $value . '</span>';
}
?>
<div class="row row-offcanvas row-offcanvas-left journalgroup-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?php if ($params['active'] == 1) { ?>
            <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4 || array_key_exists(Yii::$app->session->get('user.uteacher'), $teachers)) { ?>
                <?= Html::a('<span class="fa fa-pencil" aria-hidden="true"></span> '.Yii::t('app','Edit lesson'), ['journalgroup/update', 'id' => $model->id, 'gid' => $params['gid']], ['class' => 'btn btn-block btn-primary']) ?>
            <?php } ?>
            <?php foreach ($items as $item) { ?>
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
        <?= $this->render('_form', [
            'model'    => $model,
            'teachers' => $teachers,
        ]) ?>
    </div>
</div>
