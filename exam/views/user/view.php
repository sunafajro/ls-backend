<?php

/**
 * @var View $this
 * @var User $user
 */

use exam\models\User;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = Yii::$app->name;
$this->params['breadcrumbs'][] = ['url' => ['user/index'], 'label' => Yii::t('app', 'Users')];
$this->params['breadcrumbs'][] = $user->name;
?>
<div class="user-view">
    <?= DetailView::widget([
        'model' => $user,
        'attributes' => [
            'id',
            'login',
            'name',
            'status' => [
                'attribute' => 'status',
                'value' => function(User $user) {
                    return $user->role->name ?? '';
                }
            ],
        ],
    ]) ?>
</div>
