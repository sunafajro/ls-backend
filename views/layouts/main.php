<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

$uid = !Yii::$app->user->isGuest ? Yii::$app->session->get('user.uid') : NULL;
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
    <?php if(!Yii::$app->user->isGuest): ?>
        <div id="react-navigation-root">
            <nav id="top-nav" class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" data-toggle="collapse" data-target="#top-nav-collapse" class="navbar-toggle">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div id="top-nav-collapse" class="collapse navbar-collapse">
                        <ul id="nav-links" class="navbar-nav nav">
                            <li style="padding: 13px 10px">
                                <span class="label label-default" title="Время до автоматического выхода из системы">14:53</span>
                            </li>
                            <li>
                                <span class="navbar-text">Загружаем панель навигации...</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    <?php endif; ?>

    <div class="container-fluid">
        <?php
            if(!Yii::$app->user->isGuest){
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
                ]);
                } else {
                echo "<br />";
            }
            ?>
        <?= $content ?>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <span class="text-muted">&copy; Школа иностранных языков "Язык для Успеха" <?= date('Y') ?></span>
        </div>
    </footer>
<?php $this->endBody() ?>
<?php
    if(!Yii::$app->user->isGuest) {
        $this->registerJsFile('/js/navigation/bundle.js',  ['position' => yii\web\View::POS_END]);
    }
?>
</body>
</html>
<?php $this->endPage() ?>
