<?php

use school\models\Student;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View    $this
 * @var Student $model
 * @var array   $loginStatus
 * @var int     $roleId
 */
?>
<h4><?= Yii::t('app', 'Actions') ?>:</h4>
<?= Html::a(
    '<i class="fa fa-star" aria-hidden="true"></i> ' . Yii::t('app', 'Attestations'),
    ['student-grade/index', 'id' => $model->id],
    ['class' => 'btn btn-default btn-sm btn-block']
)
?>
<?php if (in_array($roleId, [3, 4])) { ?>
    <?php if ((int) $model->active === 1) { ?>
        <?= Html::a(
            '<i class="fa fa-phone" aria-hidden="true"></i> ' . Yii::t('app', 'Call'),
            ['call/create', 'sid' => $model->id],
            ['class' => 'btn btn-default btn-sm btn-block']
        )
        ?>
        <?= Html::a(
            '<i class="fa fa-times" aria-hidden="true"></i> ' . Yii::t('app', 'To inactive'),
            ['studname/inactive', 'id' => $model->id],
            ['class' => 'btn btn-warning btn-sm btn-block']
        )
        ?>
        <?= Html::a(
            '<i class="fa fa-file" aria-hidden="true"></i> ' . Yii::t('app', 'Invoice'),
            ['invoice/index', 'sid' => $model->id],
            ['class' => 'btn btn-default btn-sm btn-block']
        )
        ?>
        <?= Html::a(
            '<i class="fa fa-rub" aria-hidden="true"></i> ' . Yii::t('app', 'Payment'),
            ['moneystud/create', 'sid' => $model->id],
            ['class' => 'btn btn-default btn-sm btn-block']
        )
        ?>
        <?= Html::a(
            '<i class="fa fa-rub" aria-hidden="true"></i> ' . Yii::t('app', 'Commission'),
            ['student-commission/create', 'sid' => $model->id],
            ['class' => 'btn btn-default btn-sm btn-block']
        )
        ?>
        <?= Html::a(
            '<i class="fa fa-file-text-o" aria-hidden="true"></i> ' . Yii::t('app', 'Receipts'),
            ['receipt/index', 'sid' => $model->id],
            ['class' => 'btn btn-default btn-sm btn-block']
        )
        ?>
        <?= Html::a(
            '<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('app', 'Detail'),
            ['studname/detail', 'id' => $model->id],
            ['class' => 'btn btn-default btn-sm btn-block']
        )
        ?>
        <?= Html::a(
            '<i class="fa fa-gift" aria-hidden="true"></i> ' . Yii::t('app', 'Sales'),
            ['salestud/create', 'sid' => $model->id],
            ['class' => 'btn btn-default btn-sm btn-block']
        )
        ?>
        <?php if (!$loginStatus['hasLogin']) { ?>
            <?= Html::a(
                Html::tag('i', '', ['class' => 'fa fa-user-plus', 'aria-hidden' => 'true']) . ' ' . Yii::t('app', 'Account'),
                ['clientaccess/create', 'sid' => $model->id],
                ['class' => 'btn btn-default btn-sm btn-block']
            )
            ?>
        <?php } else { ?>
            <?= Html::a(
                Html::tag(
                    'i',
                    '',
                    ['class' => 'fa fa-user', 'aria-hidden' => 'true']
                ) . ' ' . Yii::t('app', 'Account') . (!$loginStatus['loginActive'] ? ' (!)' : ''),
                ['clientaccess/update', 'id' => $loginStatus['id'], 'sid' => $model->id],
                ['class' => 'btn btn-default btn-sm btn-block']
            )
            ?>
        <?php } ?>
        <?= Html::a(
            '<i class="fa fa-files-o" aria-hidden="true"></i> ' . Yii::t('app', 'Contracts'),
            ['contract/create', 'sid' => $model->id],
            ['class' => 'btn btn-default btn-sm btn-block']
        )
        ?>
        <?php if ($roleId === 3) { ?>
            <?= Html::a(
                '<i class="fa fa-mobile" aria-hidden="true"></i> ' . Yii::t('app', 'Phone'),
                ['studphone/create', 'sid' => $model->id],
                ['class' => 'btn btn-default btn-sm btn-block']
            )
            ?>
        <?php } ?>
    <?php } else { ?>
        <?= Html::a(
            '<i class="fa fa-check" aria-hidden="true"></i> ' . Yii::t('app', 'To active'),
            ['studname/active', 'id' => $model->id],
            ['class' => 'btn btn-success btn-sm btn-block']
        )
        ?>
    <?php } ?>
    <?= Html::a(
        Html::tag('i', '', ['class' => 'fa fa-wrench', 'aria-hidden' => 'true']) . ' ' . Yii::t('app', 'Settings'),
        ['studname/settings', 'id' => $model->id],
        [
            'class' => 'btn btn-default btn-sm btn-block',
        ]
    )
    ?>
    <?= Html::a(
        '<i class="fa fa-pencil" aria-hidden="true"></i> ' . Yii::t('app', 'Edit'),
        ['studname/update', 'id' => $model->id],
        ['class' => 'btn btn-default btn-sm btn-block']
    )
    ?>
    <?php if ($roleId === 3) { ?>
        <?= Html::a(
            '<i class="fa fa-compress" aria-hidden="true"></i> ' . Yii::t('app', 'Merge'),
            ['studname/merge', 'id' => $model->id],
            ['class' => 'btn btn-info btn-sm btn-block']
        )
        ?>
        <?= Html::a(
            '<i class="fa fa-trash" aria-hidden="true"></i> ' . Yii::t('app', 'Delete'),
            ['studname/delete', 'id' => $model->id],
            [
                'class' => 'btn btn-danger btn-sm btn-block',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure?'),
                    'method' => 'post',
                ],
            ]
        )
        ?>
    <?php } ?>
<?php } ?>