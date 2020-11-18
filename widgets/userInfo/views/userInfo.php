<?php
/**
 * @var View $this
 * @var Auth $user
 */

use app\components\helpers\IconHelper;
use app\modules\school\models\Auth;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="well well-sm small">
    <b><?= $user->fullName ?></b>
    <?php if ($user->teacherId) { ?>
        <?= Html::a(IconHelper::icon('user'), ['teacher/view', 'id' => $user->teacherId], ['class'=>'btn btn-default btn-xs']) ?>
    <?php } ?>
    <br />
    <i><?= $user->roleName ?></i>
    <?php if ($user->roleId === 4) { ?>
    <br />
    <?= $user->officeName ?>
    <?php } ?>
</div>