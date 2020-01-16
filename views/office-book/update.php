<?php

use app\models\OfficeBook;
use Yii;
use app\widgets\Alert;
use yii\widgets\Breadcrumbs;

/**
 * @var View       $this
 * @var OfficeBook $model
 * @var array      $offices
 * @var array      $statuses
 * @var string     $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . 'Обновить учебник';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Book presence'), 'url' => ['office-book/index']];
$this->params['breadcrumbs'][] = 'Обновить учебник';
?>
<div class="row row-offcanvas row-offcanvas-left office-book-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <div style="margin-top: 1rem">
            <h4><?= Yii::t('app', 'Hints') ?>:</h4>
            <ul>
                <li>Наименование учебника не изменяется, только через администратора).</li>
                <li>Кому, когда и на какой срок выдан учебник, можно указать в поле "Комментарий".</li>
		    </ul>
        </div>
    </div>
    <div class="col-sm-10">
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
                'model'    => $model,
                'offices'  => $offices,
                'statuses' => $statuses,
        ]) ?>
    </div>
</div>