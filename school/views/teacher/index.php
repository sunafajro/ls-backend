<?php

/**
 * @var yii\web\View $this
 * @var array $teachers
 * @var array $groups
 * @var array $pages
 * @var array $teacherlangs
 * @var array $teacheroffices
 * @var array $params
 * @var array $unviewedlessons
 * @var array $jobPlace
 */

use school\models\Auth;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Teachers');
$this->params['breadcrumbs'][] = Yii::t('app','Teachers');

/** @var Auth $auth */
$auth = Yii::$app->user->identity;

$this->params['sidebar'] = [
    'languages' => ArrayHelper::map($teacherlangs ?? [], 'lid', 'lname'),
    'offices' => ArrayHelper::map($teacheroffices ?? [], 'oid', 'oname'),
    'jobStates' => ArrayHelper::map($teacherjobstates ?? [], 'fid', 'fname'),
    'urlParams' => $params,
];

$start = 1;
$end = 20;
$nextpage = 2;
$prevpage = 0;
if (Yii::$app->request->get('page')) {
    if (Yii::$app->request->get('page')>1) {
        $start = (20 * (Yii::$app->request->get('page') - 1) + 1);
        $end = $start + 19;
        if ($end>=$pages->totalCount) {
            $end = $pages->totalCount;
        }
        $prevpage = Yii::$app->request->get('page') - 1;
        $nextpage = Yii::$app->request->get('page') + 1;
    }
}
$previousPageUrl = ['teacher/index','page' => $prevpage, 'TOID' => (string)$params['TOID'], 'TLID' => (string)$params['TLID'], 'TJID' => (string)$params['TJID'], 'TSS' => (string)$params['TSS'], 'STATE' => (string)$params['STATE'], 'BD' => (string)$params['BD']];
$nextPageUrl = ['teacher/index','page'=>$nextpage, 'TOID' => (string)$params['TOID'], 'TLID' => (string)$params['TLID'], 'TJID' => (string)$params['TJID'], 'TSS' => (string)$params['TSS'], 'STATE' => (string)$params['STATE'], 'BD' => (string)$params['BD']];
?>
    <div class="row" style="margin-bottom: 0.5rem">
        <div class="col-xs-12 col-sm-3 text-left">
            <?= (($prevpage > 0) ? Html::a('Предыдущий', $previousPageUrl, ['class' => 'btn btn-default']) : '') ?>
        </div>
        <div class="col-xs-12 col-sm-6 text-center">
            <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
        </div>
        <div class="col-xs-12 col-sm-3 text-right">
            <?= (($end < $pages->totalCount) ? Html::a('Следующий', $nextPageUrl, ['class' => 'btn btn-default']) : '') ?>
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
                    if(in_array($auth->roleId, [3])) {
                ?>
                <th class="text-center">Ставка</th>
                <th class="text-center">Корп.надб.</th>
                <th class="text-center">дог/внешт</th>
                <?php } ?>
                <?php if(in_array($auth->roleId, [3, 4])) { ?>
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
                <?php if(in_array($auth->roleId, [3])) { ?>
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
    
                <?php if (in_array($auth->roleId, [3, 4])) { ?>
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
            <?= (($prevpage > 0) ? Html::a('Предыдущий', $previousPageUrl, ['class' => 'btn btn-default']) : '') ?>
        </div>
        <div class="col-xs-12 col-sm-6 text-center">
            <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
        </div>
        <div class="col-xs-12 col-sm-3 text-right">
            <?= (($end < $pages->totalCount) ? Html::a('Следующий', $nextPageUrl, ['class' => 'btn btn-default']) : '') ?>
        </div>
    </div>
    </div>
</div>
