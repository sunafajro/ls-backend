<?php
/**
 * @var View $this
 * @var array $clients
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Clients');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Clients');
$this->params['sidebar'] = ['urlParams' => $urlParams];

$canUpdate = AccessRule::checkAccess('translationclient_update');
$canDelete = AccessRule::checkAccess('translationclient_delete');
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
        <th><?= Yii::t('app','Act.') ?></th>
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
            <td class="text-center">
                <?php
                    if ($canUpdate) {
                        echo Html::a(
                                IconHelper::icon('pencil'),
                                ['translationclient/update', 'id' => $c['id']],
                                ['title' => Yii::t('app','Edit'), 'style' => 'margin-right: 2px']
                        );
                    }
                    if ($canDelete) {
                        echo Html::a(
                                IconHelper::icon('trash'),
                                ['translationclient/delete', 'id'=>$c['id']],
                                [
                                    'title' => Yii::t('app','Delete'),
                                    'data-method' => 'post',
                                    'data-confirm' => 'Вы действительно хотите удалить этого клиента?',
                                ]
                        );
                    }
                ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>