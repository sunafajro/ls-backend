    <?php

    use school\models\Auth;
    use school\models\Sale;
use school\models\searches\DiscountSearch;
use common\widgets\alert\AlertWidget;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var DiscountSearch     $searchModel 
 * @var ActiveDataProvider $dataProvider
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Discounts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discounts'), 'url' => ['sale/index']];

/** @var Auth $auth */
$auth = Yii::$app->user->identity;
$roleId = $auth->roleId;
$userId = $auth->id;
$dicountTypeLabels = Sale::getTypeLabels();
?>
<div class="row row-offcanvas row-offcanvas-left discount-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?php if ($roleId === 3 || $userId === 389) { ?>
            <h4><?= Yii::t('app', 'Actions') ?></h4>
            <?php if ($roleId === 3) { ?>
                <?= Html::a(
                        Html::tag('span', null, ['class' => 'fa fa-plus', 'aria-hidden' => 'true']) . ' ' . Yii::t('app', 'Add'),
                        ['create'],
                        ['class' => 'btn btn-success btn-sm btn-block']
                ) ?>
            <?php } ?>
            <?= Html::a(
                    Html::tag('span', null, ['class' => 'fa fa-list', 'aria-hidden' => 'true']) . ' ' . Yii::t('app', 'Approve discounts'),
                    ['salestud/index'],
                    ['class' => 'btn btn-info btn-sm btn-block']
            ) ?>
        <?php } ?>

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

        <?= AlertWidget::widget() ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => "{pager}\n{items}\n{pager}",
            'columns'      => [
                'id' => [
                    'attribute' => 'id',
                ],
                'name' => [
                    'attribute' => 'name',
                    'format'    => 'raw',
                    'value'     => function (array $sale) {
                        if ((int)$sale['procent'] === Sale::TYPE_PERMAMENT) {
                            return $sale['name'] . Html::tag('br') . "(от {$sale['base']} руб.)";
                        } else {
                            return $sale['name'];
                        }
                    }
                ],
                'value' => [
                    'attribute' => 'value',
                ],                
                'procent' => [
                    'attribute' => 'procent',
                    'filter'    => $dicountTypeLabels,
                    'value'     => function (array $sale) {
                        return Sale::getTypeLabel($sale['procent']);
                    }
                ],
                'data' => [
                    'attribute' => 'data',
                    'value'     => function (array $sale) {
                        return date('d.m.Y', strtotime($sale['data']));
                    }
                ],
                [
                    'format' => 'raw',
                    'label'  => Yii::t('app', 'Act.'),
                    'value'  => function (array $sale) use ($roleId) {
                        $actions = [];
                        if ((int)$roleId === 3) {
                            $actions[] = Html::a(
                                Html::tag('span', null, ['class' => 'fa fa-edit', 'aria-hidden' => true]),
                                ['sale/update', 'id' => $sale['id']],
                                [
                                    'class' => 'btn btn-warning btn-xs',
                                    'style' => 'margin-right: 5px',
                                    'title' => Yii::t('app', 'Update discount'),
                                ]
                            );
                            $actions[] = Html::a(
                                Html::tag('span', null, ['class' => 'fa fa-trash', 'aria-hidden' => true]),
                                ['sale/delete', 'id' => $sale['id']],
                                [
                                    'class' => 'btn btn-danger btn-xs',
                                    'data' => [
                                        'confirm' => 'Вы уверены, что хотите удалить скидку?',
                                        'method' => 'POST',
                                    ],
                                    'title' => Yii::t('app', 'Delete discount'),
                                ]
                            );
                        }

                        return join('', $actions);
                    }
                ],
            ]
        ]) ?>
    </div>
</div>