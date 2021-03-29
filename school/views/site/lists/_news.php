<?php
/**
 * @var NewsSearch $model
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use school\models\searches\NewsSearch;
use yii\helpers\Html;

/** @var Auth $auth */
$auth   = Yii::$app->user->identity;
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <?= $model->subject ?>
        <?php if (in_array($auth->id, [139])) { ?>
            <?= Html::a(
                IconHelper::icon('trash'),
                ['news/delete', 'id' => $model->id],
                [
                    'class'=>'pull-right',
                    'title'=>Yii::t('app','Delete'),
                    'style'=>'text-decoration:none;color:white',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure?'),
                        'method' => 'post',
                    ],
                ])
            ?>
            <?= Html::a(
                IconHelper::icon('pencil'),
                ['news/update', 'id' => $model->id],
                [
                    'class' => 'pull-right',
                    'title' => Yii::t('app','Edit'),
                    'style' => 'text-decoration:none;color:white;margin-right:5px']
            ) ?>
        <?php } ?>
    </div>
    <div class="panel-body">
        <p><?= $model->body ?></p>
    </div>
    <div class="panel-footer small">
        <i><?= $model->userName ?> <?= date('d.m.Y', strtotime($model->date)) ?> в <?= date('H:i', strtotime($model->date)) ?></i>
    </div>
</div>