<?php

/**
 * @var View             $this
 * @var ActiveForm       $form
 * @var Student          $student
 * @var StudentMergeForm $model
 * @var array            $log
 * @var string           $userInfoBlock
 */

use school\models\Student;
use school\models\forms\StudentMergeForm;
use common\widgets\alert\AlertWidget;
use school\widgets\sidebarButton\SidebarButtonWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Merge account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Students'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Merge account');
?>
<div class="row row-offcanvas row-offcanvas-left student-merge">
    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 col-xl-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
		<ul>
			<li>Укажите студента, данные которого необходимо присвоить текущему студенту и нажмите кнопку Перенос.</li>
			<li>После переноса данных, вернуть профиль в исходное состояние нельзя!</li>
		</ul>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= SidebarButtonWidget::widget() ?>

		<?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'id2')->textInput() ?>
        
		<div class="form-group">
            <?= Html::submitButton(Yii::t('app','Transfer'), [
                'class' => 'btn btn-info',
                'data' => [
                        'confirm' => Yii::t('app', 'Are you sure?'),
                ],
            ]) ?>
        </div>
        <?php ActiveForm::end(); ?>

        <?php if($log): ?>
        	<table class="table">
        	    <thead>
        	        <tr>
        	    	    <th>Таблица</th>
        	    	    <th>Результат переноса</th>
        	        </tr>
        	    </thead>
        	    <tbody>
        	        <?php foreach($log as $key => $value): ?>
        	        <tr>
        	    	    <td><?= $key ?></td>
        	    	    <td><?= ($value == false) ? '<span class="text-warning">данных для переноса не найдено</span>' : '<span class="text-success">данные перенесены успешно</span>' ?></td>
        	        </tr>
        	        <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>