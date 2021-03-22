<?php

/**
 * @var yii\web\View $this
 * @var array        $teacherlangs
 * @var array        $teacheroffices
 * @var string       $userInfoBlock
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = 'Система учета :: '.Yii::t('app','Teachers');
$this->params['breadcrumbs'][] = Yii::t('app','Teachers');

//составляем список языков для селектов
$slangs = ArrayHelper::map($teacherlangs ?? [], 'lid', 'lname');
//составляем список офисов для селектов
$soffices = ArrayHelper::map($teacheroffices ?? [], 'oid', 'oname');
//составляем список форм трудоустройства для селектов
$sjobstates = ArrayHelper::map($teacherjobstates ?? [], 'fid', 'fname');
?>

<div class="row row-offcanvas row-offcanvas-left schedule-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <?php
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['teacher/index'],
            ]);
        ?>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <div class="form-group">
            <input type="text" class="form-control input-sm" placeholder="Найти по имени..." name="TSS" value="<?= $params['TSS'] ? $params['TSS'] : '' ?>">
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="STATE">
                <option value="all" <?= $params['STATE'] == 'all' ? 'selected' : '' ?>><?= Yii::t('app', '-all states-') ?></option>
                <option value="0" <?= $params['STATE'] == 0 ? 'selected' : '' ?>>С нами</option>
                <option value="1" <?= $params['STATE'] == 1 ? 'selected' : '' ?>>Не с нами</option>
                <option value="2" <?= $params['STATE'] == 2 ? 'selected' : '' ?>>В отпуске</option>
                <option value="3" <?= $params['STATE'] == 3 ? 'selected' : '' ?>>В декрете</option>
            </select>
        </div>
        <div class="form-group">
            <select class='form-control input-sm' name='TOID'>";
                <option value='all'><?= Yii::t('app', '-all offices-') ?></option>
                <?php foreach ($soffices as $key => $value) { ?>
                    <option value="<?= $key ?>"<?= $key == $params['TOID'] ? ' selected' : '' ?>><?= mb_substr($value,0,13,'UTF-8') ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="TLID">
                <option value="all"><?= Yii::t('app', '-all languages-') ?></option>
                <?php foreach($slangs as $key => $value) { ?>
                    <option value="<?= $key ?>"<?= $key == $params['TLID'] ? ' selected' : '' ?>><?= mb_substr($value,0,13,'UTF-8') ?></option>
                <?php } ?>
            </select>
        </div>
        <?php if(Yii::$app->session->get('user.ustatus') == 3): ?>
		<div class="form-group">
			<select class="form-control input-sm" name="TJID">
			    <option value="all"><?= Yii::t('app', '-all forms-') ?></option>
			    <?php foreach($sjobstates as $key => $value): ?>
			    <option value="<?= $key ?>"<?= $key == $params['TJID'] ? ' selected' : '' ?>><?= mb_substr($value,0,13,'UTF-8') ?></option>
			    <?php endforeach; ?>
		    </select>
	    </div>
        <?php endif; ?>
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
        <?php if(Yii::$app->session->hasFlash('error')) { ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php } ?>
   
        <?php if(Yii::$app->session->hasFlash('success')) { ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php } ?>
    <?php
        // задаем дефолтные параметры выборки
        // первый элемент страницы 
        $start = 1;
        // последний элемент страницы
        $end = 20;
        // следующая страница
        $nextpage = 2;
        // предыдущая страница
        $prevpage = 0;
        // проверяем не задан ли номер страницы
        if(Yii::$app->request->get('page')){
            if(Yii::$app->request->get('page')>1){
                // считаем номер первой строки с учетом страницы
                $start = (20 * (Yii::$app->request->get('page') - 1) + 1);
                // считаем номер последней строки с учетом страницы
                $end = $start + 19;
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
            <?= (($prevpage > 0) ? Html::a('Предыдущий',['teacher/index','page'=>$prevpage,'TSS'=>$params['TSS'],'TOID'=>$params['TOID'],'TLID'=>$params['TLID'],'TJID'=>$params['TJID'],/*'JPID'=>$params['JPID'],*/'STATE'=>$params['STATE'],'BD'=>$params['BD']], ['class' => 'btn btn-default']) : '') ?>
        </div>
        <div class="col-xs-12 col-sm-6 text-center">
            <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
        </div>
        <div class="col-xs-12 col-sm-3 text-right">
            <?= (($end < $pages->totalCount) ? Html::a('Следующий',['teacher/index','page'=>$nextpage,'TSS'=>$params['TSS'],'TOID'=>$params['TOID'],'TLID'=>$params['TLID'],'TJID'=>$params['TJID'],/*'JPID'=>$params['JPID'],*/'STATE'=>$params['STATE'],'BD'=>$params['BD']], ['class' => 'btn btn-default']) : '') ?>
        </div>
    </div>
    <table class="table table-stripped table-bordered table-hover table-condensed small" style="margin-bottom: 0.5rem">
        <thead>
            <tr>
                <th class="text-center tbl-cell-5"><i class="fa fa-building" aria-hidden="true"></i></th>
                <th>Имя</th>
                <th class="text-center"><?= Yii::t('app', 'Languages') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Contacts') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Birthdate/Social') ?> <?= Html::a('', ['teacher/index', 'BD'=>1], ['class'=>'glyphicon glyphicon-sort-by-alphabet']) ?></th>
                <?php
                    if(Yii::$app->session->get('user.ustatus')==3) {
                ?>
                <th class="text-center">Ставка</th>
                <th class="text-center">Корп.надб.</th>
                <th class="text-center">дог/внешт</th>
                <?php } ?>
                <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4) { ?>
                <th class="text-center"><?= Yii::t('app','Actions') ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach($teachers as $teacher) { ?>
            <tr>
                <td class="text-center tbl-cell-5">
                <?php
                if (!empty($teachertax)) {
                  $places = []; 
                  foreach($teachertax as $tax) {
                    if ($tax['tid']==$teacher['tid']) {
                        $str  = '<span class="label ' . ((int)$tax['tjplace'] === 1 ? 'label-success' : 'label-info') . '">';
                        $str .= $jobPlace[$tax['tjplace']] . '</span>';
                        $places[$tax['tjplace']] = $str;
                    }
                  }
                  ksort($places);
                  echo implode('<br />', $places);
                }
                ?>
                </td>
	            <td><?= Html::a($teacher['tname'] . ' →', ['teacher/view','id'=>$teacher['tid']]) ?>
               	<?php foreach($unviewedlessons as $uvl) {
                    if($uvl['tid']==$teacher['tid']) { ?>
                        <span class='label label-primary'><?= $uvl['lcount'] ?></span>
                    <?php } ?>
                <?php } ?>
                <?php unset($uvl); ?>
                    <br>
                    <a href="#collapse-<?= $teacher['tid'] ?>" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-<?= $teacher['tid'] ?>" class="text-warning">показать/скрыть действующие группы</a>
                    <div class="collapse" id="collapse-<?= $teacher['tid'] ?>">
            		<?php foreach($groups as $group) {
            			if($group['tid']==$teacher['tid']) { ?>				
            				<p><?= Html::a('#'.$group['gid'].' '.$group['sname'].' (усл.#'.$group['sid'].'), '.$group['ename'].' →',['groupteacher/view','id'=>$group['gid']]) ?>
                            <br>
            				<?= $group['oname'] ?>, <?= $group['gdate'] ?>, к-во: <?= $group['pupil'] ?>
            				</p>
            			<?php } ?>
            		<?php } ?>
                    </div>
                </td>
                <td class="text-center" width="10%">
                    <?php foreach($teacherlangs as $tlang) {
                        if($tlang['tid']==$teacher['tid']) { ?>
                            <?= $tlang['lname'] ?><br/>
                        <?php } ?>
                    <?php } ?>
	                <?php unset($tlang); ?>
                <td class="text-center" width="11%"><?= $teacher['tphone'] ?>
                    <br><em><?= $teacher['temail'] ?></em>
                </td>
                <td class="text-center" width="12%">
                    <?php if($teacher['bd']!=NULL) { ?>
                    <span class="text-success"><em><?= date('d.m.y', strtotime($teacher['bd'])) ?></em></span><br />
                    <?php } ?>
                    <?php if($teacher['url']!=NULL) {
                        echo Html::a('', 'http://'.$teacher['url'], ['class'=>'glyphicon glyphicon-new-window', 'target'=>'_blank', 'title'=>Yii::t('app', 'Link to social profile')]);
                    } ?>
                </td>
                <?php if(Yii::$app->session->get('user.ustatus')==3) { ?>
                <td class="text-center" width="8%">
                <?php
                if (!empty($teachertax)) {
                  $taxes = []; 
                  foreach($teachertax as $tax) {
                    if ($tax['tid']==$teacher['tid']) {
                        $str  = '<span class="label ' . ((int)$tax['tjplace'] === 1 ? 'label-success' : 'label-info') . '">';
                        $str .= $tax['taxname'] . '</span>';
                        $taxes[$tax['tjplace']] = $str;
                    }
                  }
                  ksort($taxes);
                  echo implode('<br />', $taxes);
                }
                ?>
                </td>
                <td class="text-center" width="8%"><?= ($teacher['corp'] != 0) ? $teacher['corp'].' р.' : '' ?></td>
                <td class="text-center" width="8%">
                <?= ($teacher['tstjob'] == 1) ? Html::img('@web/images/day.png', ['title'=>'Внештатник']) : Html::img('@web/images/night.png', ['title'=>'По трудовому договору']) ?></td>
                <?php } ?>
    
                <?php if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) { ?>
                <td class="text-center" width="5%">
                <?php
                echo Html::a('', ['teacher/update','id'=>$teacher['tid']], ['class'=>'glyphicon glyphicon-pencil', 'title'=>Yii::t('app','Edit')]);
                echo " ";
                echo Html::a('+G', ['groupteacher/create', 'tid'=>$teacher['tid']], ['title'=>Yii::t('app','Add group')]);
                echo " ";
                echo Html::a("+L",['langteacher/create','tid'=>$teacher['tid']], ['title'=>Yii::t('app','Add language')]); ?>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="row" style="margin-bottom: 0.5rem">
        <div class="col-xs-12 col-sm-3 text-left">
            <?= (($prevpage > 0) ? Html::a('Предыдущий',['teacher/index','page'=>$prevpage,'TSS'=>$params['TSS'],'TOID'=>$params['TOID'],'TLID'=>$params['TLID'],'TJID'=>$params['TJID'],/*'JPID'=>$params['JPID'],*/'STATE'=>$params['STATE'],'BD'=>$params['BD']], ['class' => 'btn btn-default']) : '') ?>
        </div>
        <div class="col-xs-12 col-sm-6 text-center">
            <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
        </div>
        <div class="col-xs-12 col-sm-3 text-right">
            <?= (($end < $pages->totalCount) ? Html::a('Следующий',['teacher/index','page'=>$nextpage,'TSS'=>$params['TSS'],'TOID'=>$params['TOID'],'TLID'=>$params['TLID'],'TJID'=>$params['TJID'],/*'JPID'=>$params['JPID'],*/'STATE'=>$params['STATE'],'BD'=>$params['BD']], ['class' => 'btn btn-default']) : '') ?>
        </div>
    </div>
    </div>
</div>
