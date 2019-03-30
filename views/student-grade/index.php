<?php
/**
 * @var $this          yii\web\View
 * @var $form          yii\widgets\ActiveForm
 * @var $model         app\models\StudentGrades
 * @var $student       app\models\Student
 * @var $grades
 * @var $gradeTypes 
 * @var $userInfoBlock
 */
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
$this->title = 'Система учета :: ' . Yii::t('app', 'Add attestation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view','id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add attestation');
?>
<div class="row row-offcanvas row-offcanvas-left student_grade-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
	</div>
	<div id="content" class="col-sm-10">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php if (Yii::$app->session->hasFlash('error')) { ?>
		  <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
          </div>
        <?php } ?>
        <?php if (Yii::$app->session->hasFlash('success')) { ?>
		  <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success'); ?>
          </div>
        <?php } ?> 
        <?php if (
            (int)Yii::$app->session->get('user.ustatus') === 3
            || (int)Yii::$app->session->get('user.ustatus') === 4
        ) { ?>
            <?= $this->render('_form', [
                'model' => $model,
                'gradeTypes' => $gradeTypes,
                'studentId' => $student->id,
            ]) ?>
        <?php } ?>
        <table class="table table-bordered table-hover table-stripped table-condensed">
          <thead>
            <tr>
              <th><?= Yii::t('app', 'Date') ?></th>
              <th><?= Yii::t('app', 'Description') ?></th>
              <th><?= Yii::t('app', 'Score') ?></th>
              <th><?= Yii::t('app', 'Added by') ?></th>
              <?php if (
                (int)Yii::$app->session->get('user.ustatus') === 3
                || (int)Yii::$app->session->get('user.ustatus') === 4
              ) { ?>
                <th><?= Yii::t('app', 'Actions') ?></th>
              <?php } ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($grades as $grade) { ?>
              <tr>
                <td><?= date('d.m.Y', strtotime($grade['date'])) ?></td>
                <td><?= $grade['description'] ?></td>
                <td><?= $grade['score'] ?><?= (int)$grade['type'] === 1 ? '%' : '' ?></td>
                <td><?= $grade['userName'] ?></td>
                <?php if (
                  (int)Yii::$app->session->get('user.ustatus') === 3
                  || (int)Yii::$app->session->get('user.ustatus') === 4
                ) { ?>
                  <td><?= Html::a('<i class="fa fa-trash"></i>', ['student-grade/delete', 'id' => $grade['id']]) ?></td>
                <?php } ?>
              </tr>
            <?php } ?>
          </tbody>
        </table>
    </div>
</div>