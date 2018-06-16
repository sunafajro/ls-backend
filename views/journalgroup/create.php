<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\CalcTeachergroup */
/* @var $form yii\widgets\ActiveForm */
//проверяем что идентификатор группы есть в get запросе и присваиваем его переменной $gid

$this->title = 'Система учета :: ' . Yii::t('app', 'Add lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group').' №'.$gid, 'url' => ['groupteacher/view', 'id' => $gid]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add lesson');

$script = <<< JS
$(".target").change(function() {
    var select_id = $(this).attr('id');
    var comment_id = "calcstudjournalgroup-comment_" + select_id.slice(28);
    var val = $("#" + select_id + " :selected").val();
    if(val == 1) {
        $("#" + comment_id).prop('required',true);
    } else {
        $("#" + comment_id).prop('required',false);
    }
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="row row-offcanvas row-offcanvas-left journalgroup-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if($params['active'] == 1): ?>
            <?php if(
                    (int)Yii::$app->session->get('user.ustatus') === 3 ||
                    (int)Yii::$app->session->get('user.ustatus') === 4 ||
                    (int)Yii::$app->session->get('user.uid') === 296 ||
                    array_key_exists(Yii::$app->session->get('user.uteacher'), $check_teachers)): ?>
                <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> '.Yii::t('app','Add lesson'), ['journalgroup/create','gid' => $params['gid']], ['class' => 'btn btn-block btn-primary']) ?>
            <?php endif; ?>
            <?php foreach($items as $item): ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php endforeach; ?>
        <?php endif; ?> 
        <h4>Параметры группы №<?= $params['gid']; ?></h4>
		<div class="well well-sm">
		<?php $i = 0; ?>
        <?php foreach($groupinfo as $key => $value): ?>
		    <?php if($i != 0): ?>
			<br>
            <?php endif; ?>			
            <span class="small"><b><?= $key ?>:</b></span> <span class="text-muted small"><?= $value ?></span>
			<?php $i++; ?>
        <?php endforeach; ?>
	    </div>
    </div>
	<div class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?php if(Yii::$app->session->hasFlash('error')): ?>
			<div class="alert alert-danger" role="alert">
				<?= Yii::$app->session->getFlash('error') ?>
			</div>
		<?php endif; ?>

		<?php if(Yii::$app->session->hasFlash('success')): ?>
			<div class="alert alert-success" role="alert">
				<?= Yii::$app->session->getFlash('success') ?>
			</div>
		<?php endif; ?>
        
        <h4><?= Yii::t('app', 'Add lesson to journal of group').' #'.$gid ?></h4>
        <?php $form = ActiveForm::begin(); ?>
	
	    <?= $form->field($model, 'data')->widget(DateTimePicker::className(), [
                'pluginOptions' => [
                    'language' => 'ru',
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'minView' => 2,
                    'maxView' => 2,
                    'weekStart' => 1,
                    'autoclose' => true,
                ]
            ]);
	    ?>

        <?php if(count($check_teachers) > 1): ?>
            <?= $form->field($model, 'calc_teacher')->dropDownList($items = $check_teachers, ['options' => ['1' => ['selected' => true]]]) ?>				
        <?php endif; ?>

        <?php
            if((int)Yii::$app->session->get('user.ustatus') === 3 ||
               (int)Yii::$app->session->get('user.ustatus') === 4 ||
               (int)Yii::$app->session->get('user.uid') === 296 ||
               (int)Yii::$app->session->get('user.ustatus') === 10): ?>
               <?= $form->field($model, 'calc_edutime')->dropDownList($items=$times, ['options' => ['2' => ['selected' => true]]]) ?>
        <?php endif; ?>

	    <?= $form->field($model, 'description')->textArea(['rows'=>3]) ?>

           <?= $form->field($model, 'homework')->textArea(['rows'=>3]) ?>

	    <?php foreach($students as $student): ?>

	    <div class="form-group field-calcstudjournalgroup-student_<?= $student['id'] ?>">
			<label class="control-label" for="calcstudjournalgroup-comment_<?= $student['id'] ?>"><?= $student['name'] ?></label>
			<div class="row">
			    <div class="col-sm-6">
				    <input type="text" id="calcstudjournalgroup-comment_<?= $student['id'] ?>" class="form-control" name="CalcStudjournalgroup[comment_<?= $student['id'] ?>]" required>
				</div>
				<div class="col-sm-6">
					<select class="form-control target" id="calcstudjournalgroup-status_<?= $student['id'] ?>" name="CalcStudjournalgroup[status_<?= $student['id'] ?>]">
					<?php foreach($statuses as $status) { ?>
						<option value="<?= $status['id'] ?>"><?= (int)$status['id'] === 3 ? 'не было' : $status['name'] ?></option>
					<?php } ?>
				    </select>
			    </div>
			</div>
		</div>
			
	    <?php endforeach; ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
