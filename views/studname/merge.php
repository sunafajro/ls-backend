<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Система учета :: '.Yii::t('app','Merge account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Students'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id'=>$student->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Merge account');
?>
<div class="row row-offcanvas row-offcanvas-left student_phone-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Укажите студента, данные которого необходимо присвоить текущему студенту и нажмите кнопку Перенос.</li>
			<li>После переноса данных, вернуть профиль в исходное состояние нельзя!</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        
        <?php if(Yii::$app->session->hasFlash('error')): ?>
		    <div class="alert alert-danger" role="alert"><?= Yii::$app->session->getFlash('error') ?></div>
        <?php endif; ?>    
        <?php if(Yii::$app->session->hasFlash('success')): ?>
		    <div class='alert alert-success' role='alert'><?= Yii::$app->session->getFlash('success'); ?></div>
        <?php endif; ?>
        
		<?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'id2')->widget(
            AutoComplete::className(), [
                'clientOptions' => [
                    'source' =>Url::to(['call/autocomplete']),
                    'minLength'=>'3',				
                ],
                'options'=>[
                    'class'=>'form-control',
                    'placeholder' => 'Начните набирать имя...',
                ]
            ]);
        ?>
        
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