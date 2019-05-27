<?php

/**
 * @var \yii\web\View $this
 * @var array         $fileList
 * @var string        $userInfoBlock
 */

use Yii;
use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Documents');
$this->params['breadcrumbs'][] = Yii::t('app','Documents');
?>
<div class="row row-offcanvas row-offcanvas-left document-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
	</div>
	<div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= Alert::widget() ?>
        <table class="table table-bordered table-stripped table-hover table-condensed">
            <thead>
                <th>№</th>
                <th><?= Yii::t('app', 'File') ?></th>
                <th><?= Yii::t('app', 'Act.') ?></th>
            </thead>
            <tbody>
                <?php foreach($fileList ?? [] as $key => $file) { ?>
                    <tr>
                        <td style="width: 5%"><?= $key + 1 ?></td>
                        <td><?= $file['fileName'] ?></td>
                        <td style="width: 10%">
                            <?= Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-download', 'aria-hidden' => 'true']),
                                [
                                    'document/download',
                                    'id' => $file['fileHash'],
                                ],
                                [
                                    'title' => Yii::t('app', 'Download'),
                                ]) ?>
                            <?php if ((int)Yii::$app->session->get('user.ustatus') === 3) { ?>
                            <?= Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => 'true']),
                                [
                                    'document/delete',
                                    'id' => $file['fileHash'],
                                ],
                                [
                                    'title' => Yii::t('app', 'Delete'),
                                    'data-method' => 'POST',
                                ]) ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>