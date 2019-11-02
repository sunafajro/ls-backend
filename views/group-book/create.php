<?php

use app\models\GroupBook;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View      $this
 * @var GroupBook $model
 * @var array     $books
 * @var array     $groupBooks
 * @var array     $groupInfo
 * @var array     $items
 * @var array     $params
 * @var string    $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add book');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group') . ' №' . $model->group_id, 'url' => ['groupteacher/view', 'id' => $model->group_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');
$roleId = (int)Yii::$app->session->get('user.ustatus');
$groupParams = [];
foreach($groupInfo as $key => $value) {
    $groupParams[] = Html::tag('b', $key . ':') . ' ' . $value;
}
?>
<div class="row row-offcanvas row-offcanvas-left group-book-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if ($params['active'] == 1) { ?>
            <?php if (in_array($roleId, [3, 4])) { ?>
                <?= Html::a(
                        Html::tag(
                            'span',
                            null,
                            ['class' => 'fa fa-plus', 'aria-hidden' => 'true']
                        ) . ' ' . Yii::t('app', 'Add lesson'),
                        ['journalgroup/create','gid' => $params['gid']],
                        ['class' => 'btn btn-default btn-block']
                    ) ?>
            <?php } ?>
            <?php foreach($items as $item) { ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php } ?>
        <?php } ?>
        <h4>Параметры группы №<?= $params['gid'] ?></h4>
		<div class="well well-sm">
            <?= join(Html::tag('br'), $groupParams) ?>
	    </div>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= Alert::widget() ?>
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
