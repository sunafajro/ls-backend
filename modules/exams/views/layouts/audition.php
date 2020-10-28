<?php

/**
 * @var View   $this
 * @var string $content
 */

use yii\helpers\Html;
use yii\web\View;
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
            <?= $content ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>