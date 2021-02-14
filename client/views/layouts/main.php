<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\components\helpers\IconHelper;
use common\components\helpers\RequestHelper;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use client\assets\AppAsset;

AppAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/jpg', 'href' => '/favicon-32x32.jpg', 'sizes' => '32x32']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php if (!Yii::$app->user->isGuest) {
        NavBar::begin([
            'brandLabel' => Yii::$app->params['siteName'] . ' <small>β</small>',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']],
                ['label' => Yii::t('app', 'Profile'), 'url' => ['/student/profile']],
                ['label' => Yii::t('app', 'Payments'), 'url' => ['/student/payments']],
                [
                    'label' => Yii::t('app', 'Lessons'),
                    'url' => ['/student/courses'],
                    'active' => ($this->params['navActiveLink'] ?? '') === 'courses',
                ],
                ['label' => Yii::t('app', 'Attestations'), 'url' => ['/student/attestations']],
                ['label' => Yii::t('app', 'Messages'), 'url' => ['/student/messages']],
                ['label' => Yii::t('app', 'Settings'), 'url' => ['/student/settings']],
                Yii::$app->user->isGuest
                    ? ['label' => Yii::t('app','Login'), 'url' => ['site/login']]
                    : Html::tag(
                            'li',
                            Html::a(
                                    IconHelper::icon('sign-out-alt', Yii::t('app', 'Logout'), 'fa5'),
                                    ['site/logout'],
                                    RequestHelper::createLinkPostOptions()
                            ),
                    )
            ],
        ]);
        NavBar::end();
    } ?>

    <div class="container">
        <?php if (!Yii::$app->user->isGuest) {
            echo Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]);
        } ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->params['siteCompany'] ?> <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
