<?php

/**
 * @var View     $this
 * @var UserForm $userForm
 * @var array    $roles
 */

use exam\models\forms\UserForm;
use common\widgets\alert\AlertB4Widget;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'];
$this->params['breadcrumbs'][] = 'Создать пользователя';
?>
<div>
    <?= AlertB4Widget::widget() ?>
    <?= $this->render('_form', [
            'userForm'   => $userForm,
            'roles'      => $roles,
            'isNewModel' => true,
    ]) ?>
</div>