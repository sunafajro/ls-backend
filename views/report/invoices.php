<?php
/**
 * @var yii\web\View           $this
 * @var yii\widgets\ActiveForm $form
 * @var array                  $dates
 * @var array                  $offices
 * @var string                 $oid
 * @var array                  $reportlist
 * @var string                 $userInfoBlock
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use kartik\datetime\DateTimePicker;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Journals report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Journals report');
?>
<div class="row row-offcanvas row-offcanvas-left report-invoices">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>	
        <?= $userInfoBlock ?>
        <?php if(!empty($reportlist)): ?>
        <div class="dropdown">
			<?= Html::button('<span class="fa fa-list-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Reports') . ' <span class="caret"></span>', ['class' => 'btn btn-default dropdown-toggle btn-sm btn-block', 'type' => 'button', 'id' => 'dropdownMenu', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'true']) ?>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                <?php foreach($reportlist as $key => $value): ?>
                <li><?= Html::a($key, $value, ['class'=>'dropdown-item']) ?></li>
                <?php endforeach; ?>
			</ul>            
		</div>
        <?php endif; ?>
		<h4>Фильтры</h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['report/invoices'],
            ]);
        ?>
        <div class="form-group">
            <b>Начало периода:</b>
            <?= DateTimePicker::widget([
                'name' => 'start',
                'pluginOptions' => [
                    'language' => 'ru',
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'minView' => 2,
                        'maxView' => 4,
                        'weekStart' => 1,
                        'autoclose' => true,
                ],
                'type' => DateTimePicker::TYPE_INPUT,
                'value' => $start,
            ]);?>
        </div>
        <div class="form-group">
            <b>Конец периода:</b>
            <?= DateTimePicker::widget([
                'name' => 'end',
                'pluginOptions' => [
                    'language' => 'ru',
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'minView' => 2,
                        'maxView' => 4,
                        'weekStart' => 1,
                        'autoclose' => true,
                ],
                'type' => DateTimePicker::TYPE_INPUT,
                'value' => $end,
            ]);?>
        </div>
        <?php if ((int)Yii::$app->session->get('user.ustatus') === 3) { ?>
            <div class="form-group">
			    <select name="oid" class="form-control input-sm">
			        <option value>-все офисы-</option>
			        <?php foreach ($offices as $key => $value) { ?>
                        <option value="<?= $key ?>" <?= (int)$oid === (int)$key ? 'selected' : ''?>>
                            <?= mb_substr($value, 0, 16) ?>
                        </option>
			        <?php } ?>
			    </select>
            </div>
        <?php } ?>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
	</div>
    <div class="col-sm-10">
    <?php $totalsum = 0; ?>
    <?php foreach ($dates as $key => $value) { ?>
        <a
          href="#collapse-invoice-<?= $key ?>"
          role="button"
          data-toggle="collapse" aria-expanded="false"
          aria-controls="collapse-invoice-<?= $key ?>"
          class="text-warning">
            <?= date('d.m.y', strtotime($value)) ?> (<?= Yii::t('app', date('l', strtotime($value))) ?>)
        </a>
        <br />
        <div class="collapse" id="collapse-invoice-<?= $key ?>">
        <?php $totaldaysum = 0; ?>
            <table class="table table-bordered table-stripped table-hover table-condensed">
                <tbody>
                <?php foreach ($invoices as $inv) { ?>
                    <?php if ($inv['date'] === $value) { ?>
                        <?php if ((int)$inv['visible'] === 0) { ?>
                            <tr class="danger">
                        <?php } else { ?>
                            <?php if ((int)$inv['done'] === 1) { ?> 
                                <tr class="success">
                            <?php } else { ?>
                                <tr class="warning">
                            <?php } ?>
                        <?php } ?>
                        <td>#<?= $inv['iid'] . ((int)$inv['remain'] === 1 ? ' (ост.)' : '') ?></td>
                        <td><?= $inv['uname'] ?></td>
                        <td><?= Html::a($inv['sname'] . " → ", ['studname/view', 'id' => $inv['sid']]) ?> (усл. #<?= $inv['id'] ?>, <?= $inv['num'] ?> зан.)</td>
                        <td><?= $inv['money'] ?></td>
                        </tr>
                        <?php if ((int)$inv['visible'] === 1 && (int)$inv['remain'] === 0) { ?>
                            <?php $totaldaysum = $totaldaysum + $inv['money']; ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <p class="text-right">всего за день: <?= $totaldaysum ?></p>
        <?php $totalsum = $totalsum + $totaldaysum; ?>
        <?php } ?>
        <hr />
        <p class="text-right">всего по офису: <?= $totalsum ?></p>
    </div>
</div>