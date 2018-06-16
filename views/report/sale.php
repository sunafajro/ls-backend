<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Система учета :: ' . Yii::t('app','Sale report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Sale report');

?>
<div class="row row-offcanvas row-offcanvas-left report-margin">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if(!empty($reportlist)): ?>
        <div class="dropdown">
			<?= Html::button('<span class="fa fa-list-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Reports') . ' <span class="caret"></span>', ['class' => 'btn btn-default dropdown-toggle btn-sm btn-block', 'type' => 'button', 'id' => 'dropdownMenu', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'true']) ?>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                <?php foreach($reportlist as $key => $value): ?>
                <li><?= Html::a($key, $value, ['class'=>'dropdown-item']) ?></li>
                <?php endforeach; ?>
			</ul>            
		</div>
        <?php endif; ?>
    </div>
    <div id="content" class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?php if(Yii::$app->session->hasFlash('error')) { ?>
			<div class="alert alert-danger" role="alert">
				<?= Yii::$app->session->getFlash('error') ?>
			</div>
		<?php } ?>

		<?php if(Yii::$app->session->hasFlash('success')) { ?>
			<div class="alert alert-success" role="alert">
				<?= Yii::$app->session->getFlash('success') ?>
			</div>
		<?php } ?>
        
        <nav aria-label="pager-block">
            <ul class="pager">
                <?php if($params['page'] > 1 && $params['page'] <= $params['page_count']): ?>
                <li class="previous"><?= Html::a('<span aria-hidden="true">&larr;</span> ' . Yii::t('app', 'Previous'), ['report/sale', 'page' => $params['page'] - 1]) ?></li>
                <?php endif; ?>
                <?php if($params['page'] >= 1 && $params['page'] < $params['page_count']): ?>
                <li class="next"><?= Html::a(Yii::t('app', 'Next') . '<span aria-hidden="true">&rarr;</span>', ['report/sale', 'page' => $params['page'] + 1]) ?></li>
                <?php endif; ?>
            </ul>
        </nav>

        <?php if(!empty($sales)): ?>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <?php foreach($sales as $s): ?>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading-<?= $s['sale_id'] ?>">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?= $s['sale_id'] ?>" aria-expanded="true" aria-controls="collapse-<?= $s['sale_id'] ?>">
                        <?= $s['sale'] ?>
                    </a>
                    <?= Html::a('<span class="fa fa-trash" aria-hidden="true"></span>', ['salestud/disableall', 'sid' => $s['sale_id']], ['class'=>'btn btn-danger btn-xs pull-right', 'title' => Yii::t('app', 'Disable all')]) ?>
                </div>
                <div id="collapse-<?= $s['sale_id'] ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?= $s['sale_id'] ?>">
                    <div class="panel-body">
                        <ul>
                            <?php foreach($clients as $c): ?>
                            <?php if($c['sale_id'] == $s['sale_id']): ?>
                            <li><?= Html::a('#' . $c['id'] . ' ' . $c['name'], ['studname/view', 'id' => $c['id']]) ?></li>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p class="text-center"><img src="/images/404-not-found.jpg" class="rounded" alt="По вашему запросу ничего не найдено..."></p>
        <?php endif; ?>

        <nav aria-label="pager-block">
            <ul class="pager">
                <?php if($params['page'] > 1 && $params['page'] <= $params['page_count']): ?>
                <li class="previous"><?= Html::a('<span aria-hidden="true">&larr;</span> ' . Yii::t('app', 'Previous'), ['report/sale', 'page' => $params['page'] - 1]) ?></li>
                <?php endif; ?>
                <?php if($params['page'] >= 1 && $params['page'] < $params['page_count']): ?>
                <li class="next"><?= Html::a(Yii::t('app', 'Next') . '<span aria-hidden="true">&rarr;</span>', ['report/sale', 'page' => $params['page'] + 1]) ?></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>