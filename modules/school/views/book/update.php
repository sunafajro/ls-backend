<?php

use app\models\BookForm;
use app\widgets\Alert;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View     $this
 * @var BookForm $model
 * @var array    $languages
 * @var string   $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Update book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="row row-offcanvas row-offcanvas-left book-update">
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

        <?= Alert::widget() ?>

        <?= $this->render('_form', [
                'model'      => $model ?? null,
                'languages'  => $languages ?? [],
        ]) ?>
    </div>
</div>
