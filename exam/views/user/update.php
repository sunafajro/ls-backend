<?php

/**
 * @var View     $this
 * @var UserForm $userForm
 * @var array    $roles
 */

use exam\models\forms\UserForm;
use common\widgets\alert\AlertB4Widget;
use yii\web\View;

$this->title = Yii::$app->name;
$this->params['breadcrumbs'][] = ['url' => ['user/index'], 'label' => Yii::t('app', 'Users')];
$this->params['breadcrumbs'][] = 'Изменить пользователя ' . $userForm->id;
?>
<div>
    <?= AlertB4Widget::widget() ?>
    <?= $this->render('_form', [
            'userForm'   => $userForm,
            'roles'      => $roles,
            'isNewModel' => false,
    ]) ?>
</div>