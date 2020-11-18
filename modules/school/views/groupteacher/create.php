<?php

/**
 * @var View         $this
 * @var Groupteacher $model
 * @var array        $levels
 * @var array        $offices
 * @var array        $services
 * @var array        $teacher
 */

use app\models\Groupteacher;
use app\widgets\userInfo\UserInfoWidget;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = 'Система учета :: '.Yii::t('app', 'Add group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['teacher/index']];
$this->params['breadcrumbs'][] = ['label' => $teacher['name'], 'url' => ['teacher/view','id' => $teacher['id']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add group');
?>
<div class="row row-offcanvas row-offcanvas-left group-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <div id="main-menu"></div>
        <?php } ?>
        <?= UserInfoWidget::widget() ?>
        <ul>
            <li>Если необходимо поменять тип на Корпоративный, это делается из списка групп в карточке преподавателя.</li>
        </ul>
    </div>
    <div id="content" class="col-sm-6">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= $this->render('_form', [
                'model'    => $model,
                'services' => $services,
                'levels'   => $levels,
                'offices'  => $offices,
        ]) ?>
    </div>
</div>
