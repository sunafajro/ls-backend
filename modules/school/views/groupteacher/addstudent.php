<?php

/**
 * @var View         $this
 * @var ActiveForm   $form
 * @var Groupteacher $group
 * @var Teachergroup $model
 * @var array        $groupTeachers
 * @var array        $curstudents
 * @var array        $items
 * @var array        $params
 * @var array        $students
 */

use app\models\Groupteacher;
use app\models\Teachergroup;
use app\modules\school\models\Auth;
use app\widgets\alert\AlertWidget;
use app\widgets\groupInfo\GroupInfoWidget;
use app\widgets\groupMenu\GroupMenuWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'List of students');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group') . ' №'. $params['gid'], 'url' => ['groupteacher/view','id' => $params['gid']]];
$this->params['breadcrumbs'][] = $this->title;

/** @var Auth $user */
$user       = Yii::$app->user->identity;
$roleId     = $user->roleId;
$userId     = $user->id;
$teacherId  = $user->teacherId;
?>
<div class="row row-offcanvas row-offcanvas-left group-add-student">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
        <?php if ($params['active'] == 1) {
            echo GroupMenuWidget::widget([
                'activeItem' => 'students',
                'canCreate'  => in_array($roleId, [3, 4]) || in_array($teacherId, $groupTeachers),
                'groupId'    => $group->id,
            ]);
        } ?>
        <?= GroupInfoWidget::widget(['group' => $group]) ?>
    </div>
	<div class="col-sm-10">
        <div>
            <p class="visible-xs">
                <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">
                    <?= Yii::t('app', 'Toggle nav') ?>
                </button>
            </p>
        </div>

        <?= AlertWidget::widget() ?>

        <?php if (in_array($roleId, [3, 4])) { ?>
            <h4><?= Yii::t('app', 'Add student to group') . ' #' . $params['gid'] ?></h4>
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'calc_studname')->dropDownList($students, ['prompt'=>Yii::t('app', '-select-')]) ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        <?php } ?>
        <h4><?= Yii::t('app', 'Current students of the group') ?></h4>
        <table class='table table-hover'>
            <thead>
		        <tr>
                    <th><?= Yii::t('app', 'Student') ?></th>
                    <th><?= Yii::t('app', 'When added') ?></th>
                    <th><?= Yii::t('app', 'Who added') ?></th>
                    <th><?= Yii::t('app', 'Actions') ?></th> 
	            </tr>
            </thead>
	        <tbody>
            <?php foreach($curstudents as $curstudent): ?>
                <?php switch($curstudent['visible']){
                    case 0: echo "<tr class='danger'>"; break;
                    case 1: echo "<tr class='success'>"; break;
                    default: echo "<tr>";break;
                } ?>
                <td><?= Html::a($curstudent['name'],['studname/view','id'=>$curstudent['id']]) ?></td>
		        <td><?= $curstudent['date'] ?></td>
		        <td><?= $curstudent['user'] ?></td>
                <td>
                <?php if(in_array($roleId, [3, 4])) { ?>
                    <?php switch($curstudent['visible']){
                        case 0: echo Html::a('', ['groupteacher/restorestudent','gid' => $params['gid'] ,'sid' => $curstudent['id']], ['class'=>'fa fa-check']);
                        break;
                        case 1: echo Html::a('', ['groupteacher/delstudent','gid' => $params['gid'] ,'sid' => $curstudent['id']],['class'=>'fa fa-times']);
                        break;
                    } ?>
	            <?php } ?>
                </td>
                <?php endforeach; ?>
	            </tbody>
	    </table>
    </div>
</div>
