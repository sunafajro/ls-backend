<?php

/**
 * @var View   $this
 * @var string $content
 */

use common\widgets\alert\AlertWidget;
use school\models\Navigation;
use school\widgets\sidebarButton\SidebarButtonWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use school\assets\AppAsset;
use common\widgets\navigation\NavigationWidget;

AppAsset::register($this);
$controllerId = $this->context->id;
$actionId = $this->context->action->id;
$hideModal = $controllerId === 'message' && in_array($actionId, ['create', 'update']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/jpg', 'href' => '/favicon-32x32.jpg', 'sizes' => '32x32']);
$tag = "{$controllerId}-{$actionId}";
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
<?php
    $this->beginBody();
    if (Yii::$app->params['appMode'] !== 'bitrix' && !Yii::$app->user->isGuest) {
        echo NavigationWidget::widget([
                'model' => new Navigation(),
                'hideModal' => $hideModal,
        ]);
    } ?>
    <div class="container-fluid">
        <?php if (Yii::$app->params['appMode'] !== 'bitrix' && !Yii::$app->user->isGuest) { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]) ?>
        <?php } ?>
        <div class="<?= \Yii::$app->params['layout.2-column.main.class'] ?? 'row' ?> <?= $tag ?? '' ?>">
            <div class="<?= \Yii::$app->params['layout.2-column.sidebar.class'] ?? 'col-sm-2' ?>">
                <?= UserInfoWidget::widget() ?>
                <?= $this->params['sidebar'] ?? '' ?>
            </div>
            <div class="<?= \Yii::$app->params['layout.2-column.content.class'] ?? 'col-sm-10' ?>">
                <?= AlertWidget::widget() ?>
                <?= SidebarButtonWidget::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </div>
    <?php if (Yii::$app->params['appMode'] !== 'bitrix') { ?>
        <footer class="footer">
            <div class="container-fluid">
                <span class="text-muted">
                <?php if (\Yii::$app->user->isGuest) { ?>
                    &copy; Школа иностранных языков "Язык для Успеха" <?= date('Y') ?>
                <?php } else { ?>
                    version: <?= Yii::$app->params['appVersion'] ?? '-' ?>, env: <?= YII_ENV ?>, debug: <?= var_export(YII_DEBUG, 1) ?>
                <?php } ?>
                </span>
            </div>
        </footer>
    <?php }
    $this->endBody(); ?>
</body>
</html>
<?php $this->endPage();