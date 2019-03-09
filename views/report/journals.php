<?php
    use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use yii\widgets\Breadcrumbs;
	$this->title = 'Система учета :: '.Yii::t('app','Journals report');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
	$this->params['breadcrumbs'][] = Yii::t('app','Journals report');
?>

<div class="row row-offcanvas row-offcanvas-left report-journals">
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
                'action' => ['report/journals'],
            ]);
        ?>
        <div class="form-group">
            <input type="checkbox" name="corp" value="1" <?= (int)$corp === 1 ? 'checked' : '' ?>> Корпоративные занятия
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="tid">
                <option value="all"><?= Yii::t('app', '-all teachers-') ?></option>
                <?php if(!empty($teachers)) : ?>
                    <?php foreach($teachers as $key => $value) : ?>
                        <option value="<?= $key ?>"<?= ((int)$key === (int)$tid) ? ' selected' : ''?>>
                            <?= $value ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
	</div>
    <div class="col-sm-10">
        <?php if($teacher_names) : ?>
        <?php
            // первый элемент страницы 
            $start = 1;
            // последний элемент страницы
            $end = 10;
            // следующая страница
            $nextpage = 2;
            // предыдущая страница
            $prevpage = 0;
            // проверяем не задан ли номер страницы
            if(Yii::$app->request->get('page')){
                    if(Yii::$app->request->get('page')>1){
                    // считаем номер первой строки с учетом страницы
                        $start = (10 * (Yii::$app->request->get('page') - 1) + 1);
                    // считаем номер последней строки с учетом страницы
                        $end = $start + 9;
                    // если страничка последняя подменяем номер последнего элемента
                    if($end>=$pages->totalCount){
                        $end = $pages->totalCount;
                    }
                    // считаем номер следующей страницы
                        $prevpage = Yii::$app->request->get('page') - 1;
                    // считаем номер предыдущей страницы
                        $nextpage = Yii::$app->request->get('page') + 1;
                    }
            }
        ?>
        <div class="row" style="margin-bottom: 0.5rem">
            <div class="col-xs-12 col-sm-3 text-left">
                <?= (($prevpage > 0) ? Html::a('Предыдущий',['report/journals', 'page' => $prevpage, 'tid' => $tid, 'corp' => $corp], ['class' => 'btn btn-default']) : '') ?>
            </div>
            <div class="col-xs-12 col-sm-6 text-center">
                <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
            </div>
            <div class="col-xs-12 col-sm-3 text-right">
                <?= (($end < $pages->totalCount) ? Html::a('Следующий',['report/journals', 'page'=>$nextpage, 'tid' => $tid, 'corp' => $corp], ['class' => 'btn btn-default']) : '') ?>
            </div>
        </div>
        <?php foreach($teacher_names as $key => $value) : ?>
            <div class="row bg-info" style="padding: 10px">
                <div class="text-left col-sm-8">
                    <strong><?= $value ?></strong>
                </div>
                <div class="text-right col-sm-4">
                    <span class="label label-info" title="Количество занятий на проверке"><?= $lcount[$key]['totalCount'] ?></span>
                </div>
            </div>
            <?php foreach($groups as $g ) : ?>
                <?php if ((int)$g['tid'] === (int)$key) : ?>
                <div style="padding: 10px"><?= Html::a('#' . $g['gid'] . ' ' . $g['service'] . ', ур: ' . $g['ename'] . ' (усл.#' . $g['sid'] . ')', ['groupteacher/view', 'id' => $g['gid']]) ?></div>
                    <?php if ($lcount[$key][$g['gid']]['totalCount'] > 0) : ?>
                    <table class="table table-bordered table-stripped table-hover table-condensed' style='margin-bottom:10px">
                        <tbody>
                        <?php foreach($lessons as $l) : ?>
                            <?php if ((int)$l['gid'] === (int)$g['gid'] && (int)$key === (int)$l['tid']) : ?>
                                <tr <?=((int)$l['visible'] === 0 ? 'class="danger"' : '') ?>>
                                    <td width="5%">#<? $l['lid'] ?></td>
                                    <td width="2%"><?= ((int)$l['done'] === 1 ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : '') ?></td>
                                    <td width="15%"><?= Html::a($l['date'] . ' →',['groupteacher/view', 'id' => $l['gid'], '#' => 'lesson_' . $l['lid']]) ?></td>
                                    <td><?= $l['desc'] ?></td>
                                    <td width="5%"><?= $g['hours'] ?> ч.</td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <div class="row" style="margin-bottom: 0.5rem">
            <div class="col-xs-12 col-sm-3 text-left">
                <?= (($prevpage > 0) ? Html::a('Предыдущий',['report/journals', 'page' => $prevpage, 'tid' => $tid, 'corp' => $corp], ['class' => 'btn btn-default']) : '') ?>
            </div>
            <div class="col-xs-12 col-sm-6 text-center">
                <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
            </div>
            <div class="col-xs-12 col-sm-3 text-right">
                <?= (($end < $pages->totalCount) ? Html::a('Следующий',['report/journals', 'page'=>$nextpage, 'tid' => $tid, 'corp' => $corp], ['class' => 'btn btn-default']) : '') ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
