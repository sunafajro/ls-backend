<?php

/**
 * @var View   $this
 * @var News[] $news
 * @var array  $months
 * @var array  $urlParams
 */

use app\components\helpers\DateHelper;
use app\components\helpers\IconHelper;
use app\modules\school\models\Auth;
use app\modules\school\models\News;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'News');
$this->params['breadcrumbs'][] = Yii::t('app', 'News');
/** @var Auth $user */
$user   = Yii::$app->user->identity;
$roleId = $user->roleId;
$userId = $user->id;
?>
<div class="row row-offcanvas row-offcanvas-left student-view">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <?php if (in_array($roleId, [3])) { ?>
            <h4><?= Yii::t('app', 'Actions') ?>:</h4>
            <?= Html::a(
                    IconHelper::icon('plus') . ' ' . Yii::t('app', 'News'),
                    ['news/create'],
                    ['class' => 'btn btn-success btn-sm btn-block']
            ) ?>
        <?php } ?>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['site/index'],
                ]);
        ?>
        <div class="form-group">
            <select class="form-control input-sm" name="month">
                <option value="all"><?= Yii::t('app', '-all months-') ?></option>
                <?php foreach(DateHelper::getMonths() as $key => $value): ?>
                    <option	value="<?= $key ?>"<?= ($key==$urlParams['month']) ? ' selected' : '' ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="year">
            <?php for($i=2012; $i<=date('Y'); $i++): ?>
                <option value="<?= $i ?>"<?= ($i==$urlParams['year']) ? ' selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton(
                    IconHelper::icon('filter') . ' ' . Yii::t('app', 'Apply'),
                    ['class' => 'btn btn-info btn-sm btn-block']
            ) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?php if (!empty($news)) { ?>
	        <?php foreach ($news as $n) { ?>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<?= $n->subject ?>
						<?php if (in_array($userId, [139])) { ?>
                            <?= Html::a(
                                    IconHelper::icon('trash'),
                                    ['news/delete', 'id' => $n->id],
                                    [
                                        'class'=>'pull-right',
                                        'title'=>Yii::t('app','Delete'),
                                        'style'=>'text-decoration:none;color:white',
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Are you sure?'),
                                            'method' => 'post',
                                        ],
                                    ])
                            ?>
                            <?= Html::a(
                                    IconHelper::icon('pencil'),
                                    ['news/update', 'id' => $n->id],
                                    [
                                        'class' => 'pull-right',
                                        'title' => Yii::t('app','Edit'),
                                        'style' => 'text-decoration:none;color:white;margin-right:5px']
                                ) ?>
						<?php } ?>
					</div>
					<div class="panel-body">
						<p><?= $n->body ?></p>
					</div>
					<div class="panel-footer small">
                        <i><?= $n->user->name ?> <?= date('d.m.Y', strtotime($n->date)) ?> в <?= date('H:m', strtotime($n->date)) ?></i>
					</div>
				</div>
			<?php } ?>
        <?php } ?>
    </div>
</div>
