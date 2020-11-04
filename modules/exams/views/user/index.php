<?php
/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch         $searchModel
 * @var array              $roles
 */

use app\modules\exams\models\search\UserSearch;
use app\modules\exams\models\User;
use app\widgets\alert\AlertB4Widget;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'];
$this->params['breadcrumbs'][] = 'Пользователи';
?>
<div>
    <?= AlertB4Widget::widget() ?>
    <div class="mb-3">
        <?= Html::a(
                Html::tag('i', '', ['class' => 'fas fa-plus', 'aria-hidden' => 'true']) . ' ' . 'Добавить',
                ['user/create'],
                ['class' => 'btn btn-success']
        ) ?>
    </div>
    <?php
        try {
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'columns' => [
                    'id',
                    'name',
                    'login',
                    'status' => [
                        'attribute' => 'status',
                        'filter'    => $roles,
                        'value'     => function(User $user) use ($roles) {
                            return $roles[$user->status] ?? '';
                        },
                    ],
                    ['class' => ActionColumn::class,
                        'buttons' => [
                            'update' =>  function($url, $model) {
                                return Html::a(
                                        Html::tag('i', '', ['class' => 'fas fa-edit', 'aria-hidden' => 'true']),
                                        $url,
                                        ['title' => 'Изменить']
                                );
                            },
                            'delete' => function($url, $model) {
                                return Html::a(
                                        Html::tag('i', '', ['class' => 'fas fa-trash', 'aria-hidden' => 'true']),
                                        $url,
                                        ['title' => 'Удалить', 'data-method' => 'post']
                                );
                            }
                        ]
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            echo Alert::widget([
                'options' => [
                    'class' => 'alert-danger',
                ],
                'body' => $e->getMessage(),
            ]);
        }
    ?>
</div>