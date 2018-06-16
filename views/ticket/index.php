<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Menu;

/* @var $this yii\web\View */

$this->title = 'Система учета :: '.Yii::t('app','Tickets');
$this->params['breadcrumbs'][] = Yii::t('app','Tickets');
?>
<div class="calc-orders-index">
<?php
    $items[] = ['label' => '', 'url' => ['ticket/create'], 'linkOptions'=>['class'=>'glyphicon glyphicon-plus']];
	$items[] = ['label' => 'Исходящие', 'url' => ['index', 'type'=>1]];
	$items[] = ['label' => 'Входящие', 'url' => ['index', 'type'=>2]];
    // выводим меню
	NavBar::begin([
        'renderInnerContainer' => false,
        ]);    
	    echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-left'],
            'items' => $items,					
		]);
	NavBar::end();
?>	
	<table class="table table-stripped table-bordered">
	    <thead>
	        <tr>
			    <th>#</th>
				<th>Задача</th>
				<th>Постановщик</th>
				<?php if($type == 1) { ?>
				<th>Исполнитель</th>
				<?php } ?>
				<th>Дедлайн</th>
				<th>Статус</th>
				<?php if($type == 2) { ?>
				<th>Комментарий</th>
				<?php } ?>
				<th>Действия</th>
			</tr>
		</thead>
		<tbody>
		    <?php
		    $i = 1;
		    foreach($model as $task){ ?>
		        <tr class="<?php echo $task['color']!='primary' ? $task['color'] : 'active'; ?>">
				<td><?php echo $task['tid']; ?></td>					
				<td><a data-toggle="modal" data-target="#modal-<?php echo $task['tid']; ?>"><?php echo $task['title']; ?></a>
					<div class="modal fade modal-<?php echo $task['tid']; ?>" id="modal-<?php echo $task['tid']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-label-<?php echo $task['tid']; ?>">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="modal-label-<?php echo $task['tid']; ?></h4>"><?php echo $task['title']; ?></h4>
								</div>
							<div class="modal-body">
								<p><strong>Кому:</strong> <span class="text-primary">
								<?php
								if(isset($e_nms[$task['tid']])) {
									$str = '';
									foreach($e_nms[$task['tid']] as $key => $value) {
										$str .= $value . ', ';
									}
									echo rtrim(trim($str), ',');
								}
								?></span></p>
								<p><strong>Текст:</strong> <?php echo $task['body']; ?></p>
								<p><strong>Состояние:</strong> <span class="text-<?php echo $task['color']; ?>"><?php echo $task['status']; ?></span></p>
								<p><strong>Комментарий:</strong> <?php echo $task['comment']; ?></p>
							</div>
							<?php
							if($task['status_id']==5 && isset($e_ids[$task['tid']]) && in_array(\Yii::$app->session->get('user.uid'), $e_ids[$task['tid']])){ ?>
								<div class="modal-footer">
									<?php echo Html::a('Я внимательно прочитал!',['ticket/accept','id' => $task['tid'], 'uid' => \Yii::$app->session->get('user.uid')],['class'=>'btn btn-primary']); ?>
								</div>
							<?php } ?> 
							</div>
						</div>
					</div>					
				</td>
				<td><?php echo $task['creator']; ?></td>
				<?php if($type == 1) { ?>
				<td><?php //echo Html::a($task['executor'], ['addexecutor', 'id'=>$task['tid']]); ?>
                                <a role="button" data-toggle="collapse" href="#collapse-<?php echo $task['tid']; ?>" area-expanded="false" area-controls="collapse-<?php echo $task['tid']; ?>"><?php echo $task['executor']; ?></a>
                                <div class="collapse" id="collapse-<?php echo $task['tid']; ?>">
                                    <?php foreach($executors as $ex) {
                                          if($ex['tid'] == $task['tid']) { ?>
                                          <small><span class="label label-<?php echo $ex['color']; ?>"><?php echo $ex['uname']; ?></span> (<?php echo date('d.m.y', strtotime($ex['deadline']))?>)</small><br />
                                    <?php }} ?>
                                </div>
                                </td>
				<?php } ?>
				<td><?php echo date('d.m.y', strtotime($task['deadline'])); ?></td>
			    <td><?php echo $task['status']; ?></td>
				<?php if($type == 2) { ?>
				<td><?php echo $task['comment']; ?></td>
				<?php } ?>
				<td>
			    <?php
				if($type == 1) {
				echo Html::a('', ['ticket/update', 'id'=>$task['tid']], ['class'=>'glyphicon glyphicon-pencil', 'title'=>Yii::t('app', 'Edit')]);
				echo " ";
				echo Html::a('', ['ticket/addexecutor', 'id'=>$task['tid']], ['class'=>'glyphicon glyphicon-user', 'title'=>Yii::t('app', 'Add executor')]);
				echo " ";
				}
			    if($task['status_id']==6 && $task['creator_id']==\Yii::$app->session->get('user.uid')){
					echo Html::a('', ['ticket/publish', 'id'=>$task['tid']], ['class'=>'glyphicon glyphicon-send', 'title'=>Yii::t('app', 'Publish')]);
					echo " ";
                }
				if($type == 2 && $task['status_id']==5 && !$task['closed']) {
			    	echo Html::a('', ['ticket/accept', 'id'=>$task['tid']], ['class'=>'glyphicon glyphicon-ok', 'title'=>Yii::t('app', 'Accept')]);
					echo " ";
				}
				//if(!$task['closed'] && $task['status_id']==4 && isset($e_ids[$task['tid']]) && in_array(\Yii::$app->session->get('user.uid'), $e_ids[$task['tid']])){
				if($type == 2 && ($task['status_id']==4 || $task['status_id']==2)){
					echo Html::a('', ['ticket/adjourn', 'id'=>$task['tid']], ['class'=>'glyphicon glyphicon-share-alt', 'title'=>Yii::t('app', 'Adjourn')]);
					echo " ";
				    echo Html::a('', ['ticket/close', 'id'=>$task['tid']], ['class'=>'glyphicon glyphicon-lock', 'title'=>Yii::t('app', 'Finish')]);
				}
				if($type == 2 && ($task['status_id']==1 || $task['status_id']==3)) {
					echo " ";
				    echo Html::a('', ['ticket/resume', 'id'=>$task['tid']], ['class'=>'glyphicon glyphicon-flash', 'title'=>Yii::t('app', 'Resume')]);
				}
				echo " ";
			    if($type == 1 && $task['creator_id']==\Yii::$app->session->get('user.uid')){
				    echo Html::a('', ['ticket/disable', 'id'=>$task['tid']], ['class'=>'glyphicon glyphicon-trash', 'title'=>Yii::t('app', 'Delete')]);
					echo " ";
			    } ?>
				</td>
			    </tr>
				<?php
                $i++;				
			} ?>
		</tbody>
	</table>
</div>

