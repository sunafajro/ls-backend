<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Journalgroup */

$this->title = "Система учета :: ".Yii::t('app','Update lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group').' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lesson').' №' . $model->id];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update lesson');
?>
<div class="row row-offcanvas row-offcanvas-left journalgroup-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if($params['active'] == 1): ?>
            <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4 || array_key_exists(Yii::$app->session->get('user.uteacher'), $check_teachers)): ?>
                <?= Html::a('<span class="fa fa-pencil" aria-hidden="true"></span> '.Yii::t('app','Edit lesson'), ['journalgroup/update','id'=>$model->id, 'gid' => $params['gid']], ['class' => 'btn btn-block btn-primary']) ?>
            <?php endif; ?>
            <?php foreach($items as $item): ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php endforeach; ?>
        <?php endif; ?> 
        <h4>Параметры группы №<?= $params['gid']; ?></h4>
		<div class="well well-sm">
		<?php $i = 0; ?>
        <?php foreach($groupinfo as $key => $value): ?>
		    <?php if($i != 0): ?>
			<br>
            <?php endif; ?>			
            <span class="small"><b><?= $key ?>:</b></span> <span class="text-muted small"><?= $value ?></span>
			<?php $i++; ?>
        <?php endforeach; ?>
	    </div>
    </div>
	<div class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?php if(Yii::$app->session->hasFlash('error')): ?>
			<div class="alert alert-danger" role="alert">
				<?= Yii::$app->session->getFlash('error') ?>
			</div>
		<?php endif; ?>

		<?php if(Yii::$app->session->hasFlash('success')): ?>
			<div class="alert alert-success" role="alert">
				<?= Yii::$app->session->getFlash('success') ?>
			</div>
		<?php endif; ?>
    <?= $this->render('_form', [
        'model' => $model,
	    'teachers' => $check_teachers,
	    'times' => $times,
    ]) ?>
    </div>
</div>
