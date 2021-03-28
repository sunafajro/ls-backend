<?php

/**
 * @var View   $this
 * @var News[] $news
 * @var array  $months
 * @var array  $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use school\models\News;
use yii\helpers\Html;
use yii\web\View;

$this->title =  Yii::$app->name . ' :: ' . Yii::t('app', 'News');
$this->params['breadcrumbs'][] = Yii::t('app', 'News');
/** @var Auth $user */
$user   = Yii::$app->user->identity;
$roleId = $user->roleId;
$userId = $user->id;

$this->params['sidebar'] = ['roleId' => $roleId, 'urlParams' => $urlParams];
foreach ($news as $n) { ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?= $n->subject ?>
            <?php if (in_array($userId, [139])) { ?>
                <?= Html::a(
                        IconHelper::icon('trash'),
                        ['news/delete', 'id' => $n->id],
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
                        ['news/update', 'id' => $n->id],
                        [
                            'class' => 'pull-right',
                            'title' => Yii::t('app','Edit'),
                            'style' => 'text-decoration:none;color:white;margin-right:5px']
                    ) ?>
            <?php } ?>
        </div>
        <div class="panel-body">
            <p><?= $n->body ?></p>
        </div>
        <div class="panel-footer small">
            <i><?= $n->user->name ?> <?= date('d.m.Y', strtotime($n->date)) ?> в <?= date('H:i', strtotime($n->date)) ?></i>
        </div>
    </div>
<?php }