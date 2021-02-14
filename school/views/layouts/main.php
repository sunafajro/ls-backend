<?php

/**
 * @var View   $this
 * @var string $content
 */

use school\models\Navigation;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use school\assets\AppAsset;
use common\widgets\navigation\NavigationWidget;

AppAsset::register($this);
$hideModal = $this->context->id === 'message' && in_array($this->context->action->id, ['create', 'update']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/jpg', 'href' => '/favicon-32x32.jpg', 'sizes' => '32x32']);
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
        <?php }
        echo $content; ?>
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