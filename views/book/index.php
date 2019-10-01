<?php

use app\models\search\BookSearch;
use app\widgets\Alert;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var BookSearch         $searchModel
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Books');
$this->params['breadcrumbs'][] = Yii::t('app','Books');
?>
<div class="row row-offcanvas row-offcanvas-left book-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?></h4>
        <?= Html::a(
                Html::tag('i', '', ['class' => 'fa fa-plus', 'aria-hidden' => 'true']) .
                ' ' .
                Yii::t('app', 'Add'), ['book/create'],
                ['class' => 'btn btn-success btn-sm btn-block']
        ) ?>
    </div>
    <div class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= Alert::widget() ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => "{pager}\n{items}\n{pager}",
            'columns'      => [
                'id',
                'name',
                'author',
                'description',
                'isbn',
                'publisher',
                'language',
                'actions' => [
                    'attribute' => 'actions',
                    'format'    => 'raw',
                    'label'     => Yii::t('app', 'Act.'),
                    'value'     => function (array $book) {
                        $actions = [
                            Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-edit', 'aria-hidden' => 'true']),
                                ['book/update', 'id' => $book['id']]
                            ),
                            Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => 'true']),
                                ['book/delete', 'id' => $book['id']],
                                ['data-method' => 'POST']
                            ),
                        ];

                        return join(' ', $actions);
                    }
                ]
            ]
        ]) ?>

    </div>
</div>
