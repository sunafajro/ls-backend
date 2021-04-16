<?php
/**
 * @var View $this
 * @var Auth $user
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use school\models\Auth;
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
    <i><?= $user->roleName ?></i> <?= AccessRule::checkAccess('user_view') ? Html::a(IconHelper::icon('cogs'), ['user/view', 'id' => $user->id], ['title' => 'Настройки']) : null ?>
    <?php if ($user->roleId === 4) { ?>
    <br />
    <?= IconHelper::icon('building') ?> <?= $user->officeName ?>
    <?php } ?>
</div>