<?php

/**
 * @var View         $this
 * @var Groupteacher $group
 * @var int[]        $groupTeachers
 */

use app\models\Groupteacher;
use app\modules\school\models\Auth;
use app\widgets\alert\AlertWidget;
use app\widgets\groupMenu\GroupMenuWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . ' Группа №' . $group->id;
$this->params['breadcrumbs'][] = Yii::t('app','Group') . ' №' . $group->id;
$this->params['breadcrumbs'][] = Yii::t('app', 'Files');

/** @var Auth $user */
$user = Yii::$app->user->identity;
?>
<div class="row row-offcanvas row-offcanvas-left group-announcements">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
        <?php if ($group->visible == 1) {
            echo GroupMenuWidget::widget([
                'activeItem' => 'files',
                'canCreate'  => in_array($user->roleId, [3, 4, 10]) || in_array($user->teacherId, $groupTeachers) || $user->id === 296,
                'groupId'    => $group->id,
            ]);
        } ?>
    </div>
    <div class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <div>
            <p class="visible-xs">
                <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">
                    <?= Yii::t('app', 'Toggle nav') ?>
                </button>
            </p>
        </div>

        <?= AlertWidget::widget() ?>
    </div>
</div>