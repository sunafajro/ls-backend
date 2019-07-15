<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\navigation\NavigationWidget;

AppAsset::register($this);
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
<?php $this->beginBody() ?>
    <?php if(Yii::$app->params['appMode'] !== 'bitrix' && !Yii::$app->user->isGuest): ?>
        <?= NavigationWidget::widget() ?>
    <?php endif; ?>
    <div class="container-fluid">
        <?php if (Yii::$app->params['appMode'] !== 'bitrix' && !Yii::$app->user->isGuest) : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]) ?>
        <?php endif; ?>
        <?= $content ?>
    </div>
    <?php if (Yii::$app->params['appMode'] !== 'bitrix'): ?>
    <footer class="footer">
        <div class="container-fluid">
            <span class="text-muted">&copy; Школа иностранных языков "Язык для Успеха" <?= date('Y') ?></span>
        </div>
    </footer>
    <?php endif; ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
