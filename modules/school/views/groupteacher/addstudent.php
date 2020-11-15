<?php

/**
 * @var View         $this
 * @var ActiveForm   $form
 * @var Groupteacher $group
 * @var Teachergroup $model
 * @var array        $checkTeachers
 * @var array        $curstudents
 * @var array        $items
 * @var array        $params
 * @var array        $students
 * @var string       $userInfoBlock
 */

use app\models\Groupteacher;
use app\models\Teachergroup;
use app\widgets\alert\AlertWidget;
use app\widgets\groupInfo\GroupInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'List of students');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group') . ' №'. $params['gid'], 'url' => ['groupteacher/view','id' => $params['gid']]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row row-offcanvas row-offcanvas-left group-add-student">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if($params['active'] == 1): ?>
            <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4 || array_key_exists(Yii::$app->session->get('user.uteacher'), $checkTeachers)): ?>
                <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> '.Yii::t('app','Add lesson'), ['journalgroup/create','gid' => $params['gid']], ['class' => 'btn btn-default btn-block']) ?>
            <?php endif; ?>
            <?php foreach($items as $item): ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <?= GroupInfoWidget::widget(['group' => $group]) ?>
    </div>
	<div class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>

        <?= AlertWidget::widget() ?>

        <?php if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4): ?>
            <h4><?= Yii::t('app', 'Add student to group') . ' #' . $params['gid'] ?></h4>
            
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'calc_studname')->dropDownList($students, ['prompt'=>Yii::t('app', '-select-')]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        <?php endif; ?>
    
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
                <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4): ?>
                    <?php switch($curstudent['visible']){
                        case 0: echo Html::a('', ['groupteacher/restorestudent','gid' => $params['gid'] ,'sid' => $curstudent['id']], ['class'=>'glyphicon glyphicon-ok']);break;
                        case 1: echo Html::a('', ['groupteacher/delstudent','gid' => $params['gid'] ,'sid' => $curstudent['id']],['class'=>'glyphicon glyphicon-remove']);break;
                        default: echo "";break;
                    } ?>
	            <?php endif; ?>
                </td>
                <?php endforeach; ?>
	            </tbody>
	    </table>
    </div>
</div>
