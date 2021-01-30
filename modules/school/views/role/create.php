<?php

/**
 * @var View $this
 * @var Role $model
 */

use app\modules\school\models\Role;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Create role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['admin/roles']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="row role-create">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= $this->render('_form', [
                'model' => $model,
        ]) ?>
    </div>
</div>