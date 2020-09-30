<?php

use app\models\ClientAccess;
use app\models\Student;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View         $this
 * @var ClientAccess $model
 * @var Student      $student
 * @var array        $loginStatus
 * @var string       $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Update client login or password');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id'=>$student->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update client login or password');
?>
<div class="row row-offcanvas row-offcanvas-left student_login-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
	    <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
		<ul>
			<li>При создании уч. записи логины проверяются на уникальность.</li>
            <li>Минимальная длина пароля 8 знаков.</li>
            <li>Для разблокировки ЛК достаточно изменить пароль пользователя.</li>
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
		<?= Alert::widget() ?>
		<div style="margin-bottom: 1rem">
			<b>Последний вход в ЛК:</b> <?= date('d.m.Y', strtotime($loginStatus['lastLoginDate'])) ?><br />
			<b>Доступ в ЛК:</b> <span class="label label-<?= $loginStatus['loginActive'] ? 'success' : 'danger' ?>">
				<?= $loginStatus['loginActive'] ? 'Активен' : 'Заблокирован' ?>
			</span> <?= !$loginStatus['loginActive']
			    ? Html::a(
					Html::tag('span', null, ['class' => 'fa fa-check']),
					['clientaccess/enable', 'sid' => $student->id],
					['class' => 'btn btn-default btn-xs', 'style' => 'margin-left: 1rem', 'data-method' => 'POST', 'title' => 'Восстановить доступ в ЛК']
				) : null ?>
		</div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
