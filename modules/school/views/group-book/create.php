<?php

use app\models\GroupBook;
use app\models\Groupteacher;
use app\widgets\alert\AlertWidget;
use app\widgets\groupInfo\GroupInfoWidget;
use app\widgets\groupMenu\GroupMenuWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View         $this
 * @var Groupteacher $group
 * @var GroupBook    $model
 * @var array        $books
 * @var array        $groupBooks
 * @var array        $items
 * @var array        $params
 * @var string       $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group') . ' №' . $model->group_id, 'url' => ['groupteacher/view', 'id' => $model->group_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');
$roleId = (int)Yii::$app->session->get('user.ustatus');
?>
<div class="row row-offcanvas row-offcanvas-left group-book-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if ($params['active'] == 1) {
            echo GroupMenuWidget::widget([
                'activeItem' => 'books',
                'canCreate'  => in_array($roleId, [3, 4]),
                'groupId'    => $group->id,
            ]);
        } ?>
        <?= GroupInfoWidget::widget(['group' => $group]) ?>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= AlertWidget::widget() ?>
        <?php if (in_array($roleId, [3, 4])) { ?>
            <?= $this->render('_form', [
                'model'      => $model,
                'books'      => $books,
                'groupBooks' => $groupBooks,
            ]) ?>
        <?php } ?>
        <?= $this->render('_table', [
            'groupBooks' => $groupBooks,
        ]) ?>
    </div>
</div>
