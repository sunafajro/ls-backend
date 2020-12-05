<?php

/**
 * @var View $this
 * @var News $model
 */

use app\modules\school\models\News;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\web\View;

$this->title = 'Система учета :: ' . Yii::t('app', 'Add news');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','News'), 'url' => ['site/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add news');
?>
<div class="row news-create">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
		<ul>
			<li>Кратко опишите нововведения в системе.</li>
		</ul>
	</div>
	<div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
