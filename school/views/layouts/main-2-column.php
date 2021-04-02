<?php

/**
 * @var View   $this
 * @var string $content
 */

use common\components\helpers\AlertHelper;
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
        try {
            echo NavigationWidget::widget([
                'model' => new Navigation(),
                'hideModal' => $hideModal,
            ]);
        } catch (Exception $e) {
            echo AlertHelper::alert($e->getMessage());
        }
    } ?>
    <div class="container-fluid">
        <?php if (Yii::$app->params['appMode'] !== 'bitrix' && !Yii::$app->user->isGuest) {
            try {
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
                ]);
            } catch (Exception $e) {
                echo AlertHelper::alert($e->getMessage());
            }
        } ?>
        <div class="<?= \Yii::$app->params['layout.2-column.main.class'] ?? 'row' ?> <?= $tag ?? '' ?>">
            <div class="<?= \Yii::$app->params['layout.2-column.sidebar.class'] ?? 'col-sm-2' ?>">
                <?php
                    try {
                        echo UserInfoWidget::widget();
                    } catch (Exception $e) {
                        echo AlertHelper::alert($e->getMessage());
                    }
                ?>
                <?php if (isset($this->params['sidebar'])) {
                    if (is_string($this->params['sidebar'])) {
                        echo $this->params['sidebar'];
                    } else if (is_array($this->params['sidebar'])) {
                        if (!empty($this->params['sidebar']['viewFile'])) {
                            echo $this->render($this->params['sidebar']['viewFile'], $this->params['sidebar']['params']);
                        } else {
                            $viewFile = "//{$controllerId}/sidebars/_{$actionId}";
                            echo $this->render($viewFile, $this->params['sidebar']);
                        }
                    }
                } ?>
            </div>
            <div class="<?= \Yii::$app->params['layout.2-column.content.class'] ?? 'col-sm-10' ?>">
                <?php
                    try {
                        echo AlertWidget::widget();
                    } catch (Exception $e) {
                        echo AlertHelper::alert($e->getMessage());
                    }
                ?>
                <?php
                    try {
                        echo SidebarButtonWidget::widget();
                    } catch (Exception $e) {
                        echo AlertHelper::alert($e->getMessage());
                    }
                ?>
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