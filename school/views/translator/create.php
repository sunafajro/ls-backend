<?php

/**
 * @var View $this
 * @var Translator $model
 */

use school\models\Translator;
use common\widgets\alert\AlertWidget;
use school\widgets\sidebarButton\SidebarButtonWidget;
use school\widgets\userInfo\UserInfoWidget;

$this->title = 'Система учета :: ' . Yii::t('app', 'Create translator');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translators'), 'url' => ['translate/translators']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create translator');
?>
<div class="row row-offcanvas row-offcanvas-left translator-create">
    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 col-xl-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
		<ul>
			<li>Укажите данные нового переводчика.</li>
			<li>В поле Сайт, можно указать адрес профиля социальной сети или другой ресурс.</li>
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