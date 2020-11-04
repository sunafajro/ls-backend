<?php

use app\models\forms\BookForm;
use app\widgets\alert\AlertWidget;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View     $this
 * @var BookForm $model
 * @var array    $languages
 * @var string   $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Create book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create');
?>
<div class="row row-offcanvas row-offcanvas-left book-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?? '' ?>
    </div>
    <div class="col-sm-6">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= AlertWidget::widget() ?>

        <?= $this->render('_form', [
                'model'      => $model ?? null,
                'languages'  => $languages ?? [],
        ]) ?>
    </div>
</div>