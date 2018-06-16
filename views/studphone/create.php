<?php

use yii\helpers\Html;

$this->title = 'Система учета :: '.Yii::t('app','Add phone');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Students'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id'=>$student->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Add phone');
?>
<div class="row row-offcanvas row-offcanvas-left student_phone-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Допускается добавление студенту нескольких телефонов.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= $this->render('_form', [
            'model' => $model,
            'phones' => $phones,
        ]) ?>
        <table class="table">
            <thead>
                <tr>
                <td>Телефон</td>
                <td>Описание</td>
                <td>Действия</td>
                </tr>
            </thead>
            <tbody>
            <?php foreach($phones as $phone): ?>
                <tr>
                    <td><?= $phone['phone'] ?></td>
                    <td><?= $phone['description'] ?></td>
                    <td>
                    <?= Html::a('', ['studphone/update','id'=>$phone['id']], ['class'=>'glyphicon glyphicon-pencil', 'title'=>Yii::t('app','Edit')]) 
                    ?>
                    <?= Html::a('', ['studphone/delete','id'=>$phone['id']], 
                        [
                            'class'=>'glyphicon glyphicon-trash', 
                            'title'=>Yii::t('app','Delete'),
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure?'),
                                'method' => 'post',
                            ],
                        ]) 
                    ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
