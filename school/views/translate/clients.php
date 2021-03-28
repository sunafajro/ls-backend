<?php
/**
 * @var View $this
 * @var array $clients
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Clients');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Clients');
/** @var Auth $auth */
$auth = \Yii::$app->user->identity;
$this->params['sidebar'] = ['urlParams' => $urlParams, 'canCreate' => in_array($auth->roleId, [3, 9])];
?>
<table class="table table-stripped table-bordered table-hover table-condensed small">
<thead>
    <tr>
        <th>#</th>
        <th>Наименование орг.</th>
        <th>Адрес</th>
        <th>Контактное лицо</th>
        <th>Телефон</th>
        <th>Э.почта</th>
        <th>Комментарии</th>
        <?php if (in_array($auth->roleId, [3, 9])) { ?>
            <th><?= Yii::t('app','Act.') ?></th>
        <?php } ?>
    </tr>
</thead>
<tbody>
    <?php foreach($clients as $key => $c) { ?>
        <tr>
            <td><?= $key + 1 ?></td>
            <td><?= $c['name'] ?></td>
            <td><?= $c['address'] ?></td>
            <td><?= $c['contact'] ?></td>
            <td><?= $c['phone'] ?></td>
            <td><?= $c['email'] ?></td>
            <td><?= $c['description'] ?></td>
            <?php if (in_array($auth->roleId, [3, 9])) { ?>
                <td class="text-center">
                <?= Html::a(IconHelper::icon('pencil'), ['translationclient/update', 'id'=>$c['id']], ['title'=>Yii::t('app','Edit')]) ?>
                <?= Html::a(IconHelper::icon('trash'), ['translationclient/disable', 'id'=>$c['id']], ['title'=>Yii::t('app','Delete')]) ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>