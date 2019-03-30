<?php
/**
 * @var $this     yii\web\View
 * @var $form     yii\widgets\ActiveForm
 * @var $lessons
 * @var $offices
 * @var $soffices
 * @var $slangs
 * @var $eduforms
 * @var $ages
 * @var $teachers
 * @var $oid
 * @var $lid
 * @var $eid
 * @var $aid
 * @var $tid
 * @var $day
 * @var $days
 * @var $userInfoBlock
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
// задаем тайтл страницы
if($day >= 1 && $day <= 7) {
    $this->title = 'Система учета :: ' . Yii::t('app','Schedule') . ' - ' . $days[$day];
} else {
    $this->title = 'Система учета :: ' . Yii::t('app','Schedule') . ' - ' . Yii::t('app', 'For week');
}
$this->params['breadcrumbs'][] = Yii::t('app','Schedule');
?>
<div class="row row-offcanvas row-offcanvas-left schedule-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        <?php if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 4) : ?>
            <?= Html::a(Yii::t('app', 'Teacher hours'), ['schedule/hours'], ['class' => 'btn btn-default btn-sm btn-block']) ?>
        <?php endif; ?>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['schedule/index'],
                ]);
                ?>
            <div class="form-group">
                <select class="form-control input-sm" name="day">
                    <option value="all"><?= Yii::t('app', '-all days-') ?></option>
                    <?php foreach($days as $key => $value) { ?>
                        <option value="<?= $key ?>"<?= ($key==$day) ? ' selected' : ''?>>
                            <?= $value ?>
                        </option>
                    <?php }
                    unset($key);
                    unset($value);
                    ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="OID">
                    <option value="all"><?= Yii::t('app', '-all offices-') ?></option>
					<?php foreach($soffices as $key => $value) { ?>
                        <option value="<?= $key ?>"<?= ($key==$oid) ? ' selected' : '' ?>>
							<?= $value ?>
						</option>
                    <?php }
                    unset($key);
                    unset($value);
                    ?>
                </select> 
            </div>
            <div class="form-group">                  
                <select class="form-control input-sm" name="LID">
                    <option value="all"><?= Yii::t('app', '-all languages-') ?></option>
					<?php foreach($slangs as $key => $value) { ?>
                        <option value="<?= $key ?>"<?= ($key==$lid) ? ' selected' : '' ?>>
							<?= $value ?>
						</option>
                    <?php }
                    unset($key);
                    unset($value);
                    ?>
                </select>
            </div>
            <div class="form-group">
	            <select class="form-control input-sm" name="EID">
                    <option value="all"><?= Yii::t('app', '-all forms-') ?></option>
					<?php foreach($eduforms as $eduform) { ?>
                        <option value="<?= $eduform['eid'] ?>"<?= ($eduform['eid']==$eid) ? ' selected' : '' ?>>
							<?= $eduform['ename'] ?>
						</option>
                    <?php } 
					unset($eduform);
					?>
                </select>
            </div>
            <div class="form-group">
	            <select class="form-control input-sm" name="AID">
                    <option value="all"><?= Yii::t('app', '-all ages-') ?></option>
					<?php foreach($ages as $age) { ?>
                        <option value="<?= $age['aid'] ?>"<?= ($age['aid']==$aid) ? ' selected' : '' ?>>
							<?= $age['aname'] ?>
						</option>
                    <?php } 
					unset($age);
					?>
                </select>
            </div>
            <div class="form-group">
	            <select class="form-control input-sm" name="TID">
                    <option value="all"><?= Yii::t('app', '-all teachers-') ?></option>
					<?php foreach($teachers as $key => $value) { ?>
                        <option value="<?= $key ?>"<?= ($key==$tid) ? ' selected' : '' ?>>
							<?= $value ?>
						</option>
                    <?php }
                    unset($key);
                    unset($value);
                    ?>
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
    if(!empty($offices)) {
        //оставляем только уникальные
        $result = array_unique($offices);    
        foreach($result as $office) { ?>
        	<h3><?= $office ?></h3>
        	<table class="table table-striped table-bordered table-hover table-condensed small">
        	<thead>
				<tr>
					<th class="tbl-cell-10"><?= Yii::t('app', 'Day') ?></th>
					<th class="tbl-cell-10"><?= Yii::t('app', 'Room') ?></th>
					<th class="tbl-cell-10"><?= Yii::t('app', 'Time') ?></th>
					<th class="tbl-cell-20"><?= Yii::t('app', 'Teacher') ?></th>
					<th class="tbl-cell-35"><?= Yii::t('app', 'Service') ?></th>
                                        <th class="tbl-cell-10"><?= Yii::t('app', 'Notes') ?></th>
					<th class="tb-cell-5 text-center"><?= Yii::t('app', 'Act.') ?></th>
				</tr>
        	</thead>
            <tbody>
	        <?php foreach($lessons as $lesson) {
	            if($lesson['office']==$office) { ?>
                <tr>
                    <td class="tbl-cell-10"><?= mb_convert_case($days[$lesson['day_id']], MB_CASE_TITLE, 'UTF-8') ?></td>
            	    <td class="tbl-cell-10"><?= $lesson['room'] ?></td>
                    <td class="tbl-cell-10"><?= substr($lesson['start'],0,5) . ' - ' . substr($lesson['end'], 0, 5) ?></td>
                    <td class="tbl-cell-20">
                    <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4){
                        echo Html::a($lesson['teacher'],['teacher/view','id'=>$lesson['tid']]);
                    } elseif(Yii::$app->session->get('user.ustatus')==5 && Yii::$app->session->get('user.uteacher')==$lesson['tid']) {
                        echo Html::a($lesson['teacher'],['teacher/view','id'=>$lesson['tid']]);
                    } else {
                        echo $lesson['teacher'];
                    } ?>
                    </td>
                    <td class="tbl-cell-35">
                    <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4) {
                        echo Html::a($lesson['service'],['groupteacher/view','id'=>$lesson['gid']]);
                    } elseif(Yii::$app->session->get('user.ustatus')==5 && Yii::$app->session->get('user.uteacher')==$lesson['tid']) {
                        echo Html::a($lesson['service'],['groupteacher/view','id'=>$lesson['gid']]);
                    } else {
                        echo $lesson['service'];
                    } ?>
                    </td>
                    <td class="tbl-cell-10"><?= $lesson['notes'] ?></td>
            	    <td class="tbl-cell-5 text-center">
                    <?php
                    /* если занятие активное
                    if($lesson['visible']==1){
                        // для пользователей с ролью руководитель
                        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
                            // добавляем ссылку на отмену занятия
            	            echo Html::a('', ['schedule/disable','id'=>$lesson['lessonid']], ['class'=>'glyphicon glyphicon-remove', 'title'=>Yii::t('app','Remove')]);
                        }
                        // для пользователей других ролей которые так же преподаватели добавляем ссылку на отмену своих занятий
                        elseif(Yii::$app->session->get('user.uteacher')==$lesson['tid']){
                            echo Html::a('', ['schedule/disable','id'=>$lesson['lessonid']], ['class'=>'glyphicon glyphicon-remove', 'title'=>Yii::t('app','Remove')]);
                        } else {
                            // для всех остальных просто рисуем иконку
                            echo "<span class='glyphicon glyphicon-remove'></span>";
                        }
                    // если занятие отменено
                    } else {
                        // для пользователей с ролью руководитель
                        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
                            // добавляем ссылку на возобновления занятия
                            echo Html::a('', ['schedule/enable','id'=>$lesson['lessonid']], ['class'=>'glyphicon glyphicon-ok', 'title'=>Yii::t('app','Restore')]);
                        }
                        // для пользователей других ролей которые так же преподаватели добаляем ссылку на возобновление своих занятий
                        elseif(Yii::$app->session->get('user.uteacher')==$lesson['tid']){
                            echo Html::a('', ['schedule/enable','id'=>$lesson['lessonid']], ['class'=>'glyphicon glyphicon-ok', 'title'=>Yii::t('app','Restore')]);
                        } else {
                            // для всех остальных просто рисуем иконку
                            echo "<span class='glyphicon glyphicon-ok'></span>";
                        }
                    }*/
                    // для пользователей с ролью руководитель
                    if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
                        // добавляем ссылку на редактирование занятия
                        echo ' ' . Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', ['schedule/update','id'=>$lesson['lessonid']], ['title'=>Yii::t('app','Edit')]);
                    }
                    // для пользователей других ролей которые так же преподаватели добаляем ссылку на редактирование своих занятий
                    elseif(Yii::$app->session->get('user.uteacher')==$lesson['tid']){
                        echo ' ' . Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', ['schedule/update','id'=>$lesson['lessonid']], ['title'=>Yii::t('app','Edit')]);
                    } else { ?>
                        <span class="fa fa-pencil" aria-hidden="true"></span>
                    <?php }
                    // для пользователей с ролью руководитель
                    if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
                        // добавляем ссылку на удаление занятия
                        echo ' ' . Html::a('<span class="fa fa-trash" aria-hidden="true"></span>', ['schedule/disable','id'=>$lesson['lessonid']], ['title'=>Yii::t('app','Delete')]);
                    } else { ?>
                        <span class="fa fa-trash" aria-hidden="true"></span>
                    <?php } ?>
                    </td>
                </tr>
              	<?php } ?>
            <?php } ?>
            </tbody>
            </table>
        <?php } ?>
    <?php } else { ?>
        <p class="text-center"><img src="/images/404-not-found.jpg" class="rounded" alt="По вашему запросу ничего не найдено..."></p>
    <?php } ?>
	</div>
</div>