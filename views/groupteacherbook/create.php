<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Groupteacherbook */

$this->title = 'Система учета :: ' . Yii::t('app', 'Add book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group') . ' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');
?>
<div class="row row-offcanvas row-offcanvas-left group-book-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if($params['active'] == 1): ?>
            <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4): ?>
                <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> '.Yii::t('app','Add lesson'), ['journalgroup/create','gid' => $params['gid']], ['class' => 'btn btn-default btn-block']) ?>
            <?php endif; ?>
            <?php foreach($items as $item): ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <h4>Параметры группы №<?= $params['gid'] ?></h4>
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
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>

        <?= $this->render('_form', [
            'model' => $model,
            'gid' => $params['gid'],
            'books' => $books,
            'curr_books' => $curr_books,
        ]) ?>
    </div>
</div>
