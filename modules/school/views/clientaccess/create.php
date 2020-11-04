<?php
use app\models\ClientAccess;
use app\models\Student;
use app\widgets\alert\AlertWidget;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View         $this
 * @var ClientAccess $model
 * @var Student      $student
 * @var array        $loginStatus
 * @var string       $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Create client login & password');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id'=>$student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create client login & password');
?>

<div class="row row-offcanvas row-offcanvas-left student_login-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>При создании уч. записи логины проверяются на уникальность.</li>
            <li>Минимальная длина пароля 8 знаков.</li>
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
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
