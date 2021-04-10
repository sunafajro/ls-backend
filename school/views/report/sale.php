<?php
/**
 * @var array $sales
 * @var array $clients
 * @var array $params
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use yii\helpers\Html;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Sale report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Sale report');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'activeReport' => 'sales',
    ],
];

if(!empty($sales)) {
    $pager = [];
    $pager[] = Html::beginTag('nav', ['aria-label' => 'pager-block']);
    $pager[] = Html::beginTag('ul', ['class' => 'pager']);
    if ($params['page'] > 1 && $params['page'] <= $params['pageCount']) {
        $pager[] = Html::tag('li', Html::a('<span aria-hidden="true">&larr;</span> ' . Yii::t('app', 'Previous'), ['report/sale', 'page' => $params['page'] - 1]), ['class' => 'previous']);
    }
    if($params['page'] >= 1 && $params['page'] < $params['pageCount']) {
        $pager[] = Html::tag('li', Html::a(Yii::t('app', 'Next') . '<span aria-hidden="true">&rarr;</span>', ['report/sale', 'page' => $params['page'] + 1]), ['class' => 'next']);
    }
    $pager[] = Html::endTag('ul');
    $pager[] = Html::endTag('nav');

    echo join('', $pager); ?>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php foreach($sales as $s) { ?>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="heading-<?= $s['sale_id'] ?>">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?= $s['sale_id'] ?>" aria-expanded="true" aria-controls="collapse-<?= $s['sale_id'] ?>">
                    <?= $s['sale'] ?>
                </a>
                <?php if (AccessRule::checkAccess('salestud_disable-all')) {
                    echo Html::a(
                            IconHelper::icon('trash'),
                            ['salestud/disable-all', 'sid' => $s['sale_id']],
                            [
                                'class' => 'btn btn-danger btn-xs pull-right',
                                'title' => Yii::t('app', 'Disable all'),
                                'data-method' => 'post',
                                'data-confirm' => 'Вы уверены что хотите аннулировать скидку для всех клиентов?',
                            ]
                    );
                } ?>
            </div>
            <div id="collapse-<?= $s['sale_id'] ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?= $s['sale_id'] ?>">
                <div class="panel-body">
                    <ul>
                        <?php foreach($clients as $c) {
                            if($c['sale_id'] == $s['sale_id']) { ?>
                            <li><?= Html::a('#' . $c['id'] . ' ' . $c['name'], ['studname/view', 'id' => $c['id']]) ?></li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php echo join('', $pager);
}