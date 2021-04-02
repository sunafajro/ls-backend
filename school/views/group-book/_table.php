<?php

use common\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View  $this
 * @var array $groupBooks
 */
$roleId = (int)Yii::$app->session->get('user.ustatus');
?>
<div class="group-book-table">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th><?= Yii::t('app', 'Name') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Primary') ?></th>
                <?php if (in_array($roleId, [3, 4])) { ?>
                    <th class="text-center"><?= Yii::t('app', 'Act.') ?></th>
                <?php } ?>
            </tr>
        </thead>
        <?php foreach($groupBooks as $book) { ?>
            <tr>
                <td><?= $book['name'] ?></td>
                <td class="text-center">
                    <?= $book['primary'] ? Html::tag('span', Yii::t('app', 'Primary'), ['class' => 'label label-primary']) : '' ?>
                </td>
                <?php if (in_array($roleId, [3, 4])) { ?>
                    <td class="text-center">
                        <?= Html::a(
                                IconHelper::icon('trash'),
                                ['group-book/delete', 'id' => $book['id']],
                                ['data-method' => 'post', 'title' => Yii::t('app', 'Delete')]
                            ) ?>
                        <?= !$book['primary']
                                ? Html::a(
                                    IconHelper::icon('check'),
                                    ['group-book/primary', 'id' => $book['id']],
                                    ['data-method' => 'post', 'title' => Yii::t('app', 'Make primary')]
                                )
                                : '' ?> 
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</div>