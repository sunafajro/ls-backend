<?php

/**
 * @var View     $this
 * @var UserForm $userForm
 * @var array    $roles
 */

use app\modules\exams\models\forms\UserForm;
use app\widgets\alert\AlertB4Widget;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'];
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