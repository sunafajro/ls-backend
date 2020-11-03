<?php

/**
 * @var View   $this
 * @var string $content
 */

use app\modules\exams\assets\AppAsset;
use app\modules\exams\models\Navigation;
use app\widgets\navigation\NavigationWidget;
use yii\bootstrap4\Breadcrumbs;
use yii\helpers\Html;
use yii\web\View;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body style="padding-bottom: 60px">
        <?php $this->beginBody() ?>
            <div class="container-fluid">
                <?php if (!Yii::$app->user->isGuest) {
                    echo NavigationWidget::widget([
                        'model'     => new Navigation(),
                        'hideModal' => false,
                        'viewFile'  => 'navigation-b4',
                    ]);
                    echo Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
                    ]);
                } ?>
                <?= $content ?>
            </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>