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
    <?php if ($user->teacherId) { ?>
        <?= Html::a(
                IconHelper::icon('user') . ' ' . Html::tag('b', $user->fullName),
                ['teacher/view', 'id' => $user->teacherId],
                ['title' => 'Перейти в профиль преподавателя']) ?>
    <?php } else { ?>
        <b><?= $user->fullName ?></b>
    <?php } ?>
    <br />
    <i><?= $user->roleName ?></i> <?= $user->roleId === 4 ? Html::a(IconHelper::icon('clock-o'), ['user/view', 'id' => $user->id], ['title' => 'Учет рабочего времени']) : '' ?>
    <?php if ($user->roleId === 4) { ?>
    <br />
    <?= IconHelper::icon('building') ?> <?= $user->officeName ?>
    <?php } ?>
</div>