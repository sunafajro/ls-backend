<?php

/**
 * @var View $this
 * @var Translationnorm $model
 */

use common\widgets\alert\AlertWidget;
use school\models\Translationnorm;
use school\widgets\sidebarButton\SidebarButtonWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\web\View;

$this->title = 'Система учета :: ' . Yii::t('app', 'Create translation pay norm');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translation pay norms'), 'url' => ['translate/norms']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');
?>
<div class="row row-offcanvas row-offcanvas-left translation-norm-create">
    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 col-xl-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
		<ul>
			<li>Заполните данные новой нормы полаты и нажмите кнопку Добавить.</li>
		</ul>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= SidebarButtonWidget::widget() ?>
	    <?= $this->render('_form', [
	        'model' => $model,
	    ]) ?>
    </div>
</div>
