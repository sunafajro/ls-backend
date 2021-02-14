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
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Merge account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Students'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Merge account');
?>

<div class="row row-offcanvas row-offcanvas-left student_phone-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Укажите студента, данные которого необходимо присвоить текущему студенту и нажмите кнопку Перенос.</li>
			<li>После переноса данных, вернуть профиль в исходное состояние нельзя!</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>        
        
        <?= AlertWidget::widget() ?>

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