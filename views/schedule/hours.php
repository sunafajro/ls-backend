<?php
/**
 * @var $this     yii\web\View
 * @var $form     yii\widgets\ActiveForm
 * @var $params
 * @var $teachers
 * @var $languages
 * @var $lessons
 * @var $offices
 * @var $userInfoBlock
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
$this->title = 'Система учета :: ' . Yii::t('app','Teacher hours');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Schedule'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Teacher hours');
?>
<div class="row row-offcanvas row-offcanvas-left schedule-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        <?= Html::a(Yii::t('app', 'Schedule'), ['schedule/index'], ['class' => 'btn btn-default btn-sm btn-block']) ?>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['schedule/hours'],
                ]);
                ?>
            <div class="form-group">
                <select class="form-control input-sm" name="OID">
                    <option value="all"><?= Yii::t('app', '-all offices-') ?></option>
					<?php foreach($offices as $key => $value) : ?>
                        <option value="<?= $key ?>"<?= ($key == $params['oid']) ? ' selected' : '' ?>>
							<?= $value ?>
						</option>
                    <?php endforeach; ?>
                </select> 
            </div>
            <div class="form-group">
                <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
            </div>
        <?php ActiveForm::end(); ?>
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
			<?= Yii::$app->session->getFlash('success') ?>
        </div>
		<?php } ?>
        <table class="table table-bordered table-hover table-condensed small">
            <thead>
                <tr>
                    <th><?= Yii::t('app', 'Teacher') ?></th>
                    <th class="text-center"><?= Yii::t('app', 'Language') ?></th>
                    <th class="text-center"><?= Yii::t('app', 'Hours') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($teachers as $tid => $tname) {                
                $langs = [];
                foreach ($languages as $lid => $lname) {
                    $langs[$lid] = 0;
                    foreach ($lessons as $l) {
                        if ($l['teacher_id'] == $tid && $l['language_id'] == $lid) {
                            $langs[$lid] = $langs[$lid] + $l['hours']; 
                        }
                    }
                    if (!$langs[$lid]) {
                        unset($langs[$lid]);
                    }
                }
                $i = 0;
                foreach ($langs as $lang => $val) {
                    echo '<tr>';
                    if ($i === 0) {
                        echo '<td rowspan="' . count($langs) . '" style="vertical-align: middle">' . $tname . '</td>';
                    }
                    echo '<td class="text-center">' . $languages[$lang] . '</td>';
                    echo '<td class="text-center">' . $val . '</td>';
                    echo '</tr>';
                    $i++;
                }
            } ?>
            </tbody>
        </table>
    </div>
</div>