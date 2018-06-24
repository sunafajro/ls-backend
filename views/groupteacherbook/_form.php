<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Groupteacherbook */
/* @var $form yii\widgets\ActiveForm */
?>
<?php if (Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4) : ?>
<div class="groupteacherbook-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'calc_book')->dropDownList($item=$books, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'prime')->checkbox(); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php endif; ?>
<div class="groupteacherboook-table">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Наименование</th>
                <th>Основной</th>
                <th>Действ.</th>
            </tr>
        </thead>
        <?php foreach($curr_books as $b) { ?>
        <tr>
            <td><?php echo $b['name']; ?></td>
            <td><?php echo $b['prime'] ? '<span class="label label-primary"> ' . Yii::t('app', 'Prime') . '</span>' : ''; ?></td>
            <td>
            <?php echo Html::a('', ['delete', 'id'=>$b['id']], ['class' => 'glyphicon glyphicon-trash']); ?>
            <?php echo !$b['prime'] ? Html::a('', ['primary', 'id'=>$b['id']], ['class' => 'glyphicon glyphicon-ok', 'title'=>Yii::t('app', 'Make primary')]) : ''; ?> 
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
