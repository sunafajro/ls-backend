<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Система учета :: ' . Yii::t('app', 'News');
$this->params['breadcrumbs'][] = Yii::t('app', 'News');
?>

<div class="row row-offcanvas row-offcanvas-left student-view">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if(Yii::$app->session->get('user.ustatus') == 3): ?>
            <h4><?= Yii::t('app', 'Actions') ?>:</h4>
            <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'News'), ['news/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        <?php endif; ?>
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
                <?php foreach($months as $key => $value): ?>
                    <option	value="<?php echo $key; ?>"<?php echo ($key==$url_params['month']) ? ' selected' : ''; ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="year">
            <?php for($i=2012; $i<=date('Y'); $i++): ?>
                <option value="<?= $i ?>"<?= ($i==$url_params['year']) ? ' selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php if(Yii::$app->session->hasFlash('error')): ?>
		    <div class="alert alert-danger" role="alert"><?= Yii::$app->session->getFlash('error') ?></div>
        <?php endif; ?>    
        <?php if(Yii::$app->session->hasFlash('success')): ?>
		    <div class='alert alert-success' role='alert'><?= Yii::$app->session->getFlash('success'); ?></div>
        <?php endif; ?> 
        <?php if(!empty($news)): ?>
	    <?php foreach($news as $n): ?>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<?= $n['subject'] ?>
						<?php if(Yii::$app->session->get('user.uid')==139): ?>
                            <?= Html::a('', ['news/delete','id'=>$n['id']], 
                            [
                                'class'=>'fa fa-trash pull-right', 
                                'title'=>Yii::t('app','Delete'),
                                'style'=>'text-decoration:none;color:white',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure?'),
                                    'method' => 'post',
                                ],
                            ]) 
                        ?>
                        <?= Html::a('', ['news/update','id'=>$n['id']], ['class'=>'fa fa-pencil pull-right', 'title'=>Yii::t('app','Edit'), 'style'=>'text-decoration:none;color:white']) ?>
						<?php endif; ?>
					</div>
					<div class="panel-body">
						<p><?= $n['description'] ?></p>
					</div>
					<div class='panel-footer'>
                        <small><em><?= $n['author'] ?> <?= date('d.m.Y',strtotime($n['date'])) ?> в <?= date('H:m',strtotime($n['date'])) ?></em></small>
					</div>
				</div>
			<?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
