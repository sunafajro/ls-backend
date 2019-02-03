<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\widgets\Breadcrumbs;
    $this->title = 'Система учета :: '.Yii::t('app','Calls');
    $this->params['breadcrumbs'][] = Yii::t('app','Calls');
?>

<div class="row row-offcanvas row-offcanvas-left call-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?></h4>
        <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        <h4><?= Yii::t('app', 'Filters') ?></h4>
        <?php 
                $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['call/index'],
                    ]);
                    ?>
            <div class="form-group">
            <input type="text" class="form-control input-sm" placeholder="Найти..." name="TSS" value="<?= ($filter['tss'] != '') ? $filter['tss'] : '' ?>">
            </div>
            <div class="form-group">
        		<select class="form-control input-sm" name="STID">
                    <option value="all"><?= Yii::t('app', '-all types-') ?></option>
        		    <?php foreach($servicetypes as $st) { ?>
        			<option value="<?= $st['stid'] ?>"<?= ($st['stid']==$filter['stid']) ? ' selected' : '' ?>><?= $st['stname'] ?></option>
        			<?php }
                    unset($st);
                    ?>
        		</select>
            </div>
            <div class="form-group">
        		<select class="form-control input-sm" name="LID">
                    <option value="all"><?= Yii::t('app', '-all languages-') ?></option>
        		    <?php foreach($languages as $l){ ?>
        			<option value="<?= $l['lid'] ?>"<?= ($l['lid']==$filter['lid']) ? ' selected' : '' ?>><?= $l['lname'] ?></option>
        			<?php }
                    unset($l);
        		    ?>
        		</select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="OID">
        			<option value="all"><?= Yii::t('app', '-all offices-') ?></option>
        			<?php foreach($offices as $o) { ?>
        			<option value="<?= $o['oid'] ?>"<?= ($o['oid']==$filter['oid']) ? ' selected' : '' ?>><?= $o['oname'] ?></option>
        			<?php }
                    unset($o);
        			?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="AID">
        			<option value="all"><?= Yii::t('app', '-all ages-') ?></option>
        			<?php foreach($ages as $a) { ?>
        			<option value="<?= $a['aid'] ?>"<?= ($a['aid']==$filter['aid']) ? ' selected' : '' ?>><?= $a['aname'] ?></option>
        			<?php }
                    unset($a);
        			?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="month">
                    <option value="all"><?= Yii::t('app', '-all months-') ?></option>
                    <?php foreach($months as $mkey => $mvalue){ ?>
        			    <option	value="<?php echo $mkey; ?>"<?php echo ($mkey==$filter['month']) ? ' selected' : ''; ?>><?= $mvalue ?></option>
        			<?php }
                    unset($mkey);
                    unset($mvalue);
                     ?>
        		</select>
            </div>
            <div class="form-group">
        		<select class="form-control input-sm" name="year">
        		<?php for($i=2012;$i<=date('Y');$i++) { ?>
        			<option value="<?= $i ?>"<?= ($i==$filter['year']) ? ' selected' : '' ?>><?= $i ?></option>
                    <?php }
                    unset($i);
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
        <?php 
            if(Yii::$app->session->getFlash('success')){
                echo "<div class='alert alert-success' role='alert'>";
                echo Yii::$app->session->getFlash('success');
                echo "</div>";
                }
            if(Yii::$app->session->getFlash('error')){
                echo "<div class='alert alert-danger' role='alert'>";
                echo Yii::$app->session->getFlash('error');
                echo "</div>";
                }
            ?>
    <?php if(!empty($calls)) { ?>
        <table class="table table-striped table-bordered table-hover table-condensed small">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th><?= Yii::t('app', 'Full name') ?></th>
                    <th><?= Yii::t('app', 'Phone') ?></th>
                    <th>Вид. усл.</th>
                    <th><?= Yii::t('app', 'Language') ?></th>
                    <th>Доп. сведения</th>
                    <th>Кто и когда принял клиента</th>
                    <th class="text-center"><?= Yii::t('app', 'Act.') ?></th>
                </tr>
            </thead>
        <?php foreach($calls as $call) { ?>
	        <tr class="<?= ($call['cvisible']==0) ? 'danger' : '' ?>">
            <td class="tbl-cell-5 font-weight-bold text-center"><?= $call['cid'] ?></td>
			<td class="tbl-cell-20">
            <?php
            if(!isset($call['stid'])||$call['stid']==0){
                // если нет, посто выводим ФИО 
                echo $call['cname'];
            } else {
                // если да, выводим ФИО в виде ссылки на карточку
                echo Html::a($call['cname'],['studname/view', 'id'=>$call['stid']]);
            } ?>
			<br>
			<span class="text-muted"><?= $call['eduage'] ?></span></td>
            <td class="tbl-cell-15"><?= $call['cphone'] ?></td>
            <td class="tbl-cell-5"><?= mb_substr($call['stname'],0,6,'UTF-8')?>-</td>
			<td class="tbl-cell-5"><?= mb_substr($call['lname'],0,6,'UTF-8') ?>-</td>
    	    <td class="tbl-cell-30">
    	    <?php if($call['elname']) { ?>
                <span class="text-muted">Уровень:</span> <?= $call['elname'] ?><br />
            <?php } ?>
    	    <?php if($call['efname']) { ?>
                <span class="text-muted">Форма:</span> <?= $call['efname'] ?><br />
            <?php } ?>
    	    <?php if($call['oname']) { ?>
                <span class="text-muted">Офис:</span> <?= $call['oname'] ?><br />
            <?php } ?>
    	    <?php  if($call['cdesc']) { ?>
                <p class="text-danger"><?= $call['cdesc'] ?></p>
            <?php } ?>
    	    </td>
    	    <td class="tbl-cell-15"><?= $call['uname'] ?>
            <br />
            <span class="text-muted"><?= date('d.m.y', strtotime($call['cdate'])) ?> в <?= date('H:i', strtotime($call['cdate'])) ?></span></td>
            <td class="text-center tbl-cell-5">
            <?php if($call['cvisible']==1) {
                echo Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', ['call/update','id'=>$call['cid']], ['title'=>Yii::t('app','Edit call')])." ";
                if(!isset($call['stid'])||$call['stid']==0) {
                    if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4){
                        echo Html::a('<span class="fa fa-user" aria-hidden="true"></span>', ['call/transform', 'id'=>$call['cid']],['title'=>Yii::t('app','Create client')])." ";
                    }
                }
                if(Yii::$app->session->get('user.ustatus')==5 && Yii::$app->session->get('user.uid')!=$call['uid']) { ?>
                    <span class='fa fa-trash' aria-hidden='true'></span>";
                <?php } else { 
                    echo Html::a('<span class="fa fa-trash" aria-hidden="true"></span>', ['call/disable', 'id'=>$call['cid']], ['title'=>Yii::t('app','Delete call')]);
                }
            } else { ?>
                </td>
            <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
        </table>
        <?php } else { ?>
            <p class="text-center"><img src="/images/404-not-found.jpg" class="rounded" alt="По вашему запросу ничего не найдено..."></p>
        <?php } ?>
    </div>
</div>
