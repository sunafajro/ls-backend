<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model school\models\Ticket */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Add executor');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Tickets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => mb_substr($ticket->title, 0, 20)];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="calc-orders-form">

    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'user')->dropDownList($items=$emps) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php if($current_users) { ?>
	<h3>Исполнители, которым назначена задача:</h3>
	<?php } ?>
	<table class="table">
		<?php
		foreach($current_users as $cu) { ?>
			<tr>
			    <td><?php echo $cu['name']; ?></td>
				<td><?php echo '<span class="label label-' . $cu['color'] . '">' . $cu['state'] . '</span>'; ?></td>
				<td><?php echo $cu['comment']; ?></td>
				<td><?php echo Html::a('', ['ticket/delexecutor', 'id'=>$cu['id']], ['class'=>'glyphicon glyphicon-trash', 'title'=>Yii::t('app', 'Delete')]); ?></td>
		    </tr>
		<?php } ?>
	</table>
</div>
