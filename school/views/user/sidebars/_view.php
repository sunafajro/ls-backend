<?php
/**
 * @var View $this
 * @var User $user
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use school\models\Auth;
use school\models\User;
use yii\helpers\Html;
use yii\web\View;
?>
<?php
/** @var Auth $auth */
$auth = Yii::$app->user->identity;
if (AccessRule::checkAccess('rule.time-tracking.manage.any') || $auth->id === $user->id) { ?>
    <div style="margin-bottom:1rem">
        <h4>Действия:</h4>
        <?= Html::a(
            IconHelper::icon('clock-o', 'Учет времени'),
            ['user/time-tracking', 'id' => $user->id],
            ['class' => 'btn btn-default btn-block']
        ) ?>
    </div>
<?php } ?>
<div style="margin-bottom:1rem">
    <ul>
        <li>У пользователя может быть только одно фото. Новое загруженное фото заменяет старое.</li>
    </ul>
</div>