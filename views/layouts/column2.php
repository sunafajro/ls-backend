<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;

if(Yii::$app->session->get('user.sidebar')==2) {
    $style = 'style="display: none"';
    $class = 'class="col-sm-12"';
} else {
    $style = NULL;
    $class = 'class="col-sm-10"';
}

$this->beginContent('@app/views/layouts/main.php'); ?>
<div class="container-fluid">
    <div class="row row-offcanvas row-offcanvas-left">
    	<div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas" <?php echo $style ? $style : '' ?>>
    	    <div class="well well-sm small">
    		<?php
				echo "<strong>";
				echo Yii::$app->session->get('user.uname');
				echo "</strong>";
				if(Yii::$app->session->get('user.uteacher')) {
					echo " ";					
					echo Html::a('', ['teacher/view', 'id'=>Yii::$app->session->get('user.uteacher')], ['class'=>'glyphicon glyphicon-user btn btn-default btn-xs']);					
				}    		    
    		    echo "<br />";
    		    echo Yii::$app->session->get('user.stname');
                if(Yii::$app->session->get('user.ustatus')==4){
                    echo "<br />";
                    echo Yii::$app->session->get('user.uoffice');
                }
                echo "<br><span id='timer' class='text-danger'></span>";
    		?>
    		</div>
    		<?php
				// отчеты видны менеджерам, руководителям, бухгалтеру
				if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4 || Yii::$app->session->get('user.ustatus')==8) {
					$items[] = ['label' => '<span class="fa fa-list-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Reports'), 'url' => ['report/index']];
					$items[] = ['label' => '<span class="fa fa-money" aria-hidden="true"></span> ' . Yii::t('app', 'Expenses') . ' <span id="expense" class="badge">0</span>', 'url' => ['kaslibro/index']];
				}
                if(Yii::$app->session->get('user.ustatus')!=2 && Yii::$app->session->get('user.ustatus')!=8 && Yii::$app->session->get('user.ustatus')!=9) {
			        $items[] = ['label' => '<span class="fa fa-calendar" aria-hidden="true"></span> ' . Yii::t('app', 'Schedule'), 'url' => ['schedule/index']];
                }
			    $items[] = ['label' => '<span class="fa fa-tasks" aria-hidden="true"></span> ' . Yii::t('app', 'Tickets') . ' <span id="task" class="badge">0</span>', 'url' => ['ticket/index']];
                $items[] = ['label' => '<span class="fa fa-envelope" aria-hidden="true"></span> ' . Yii::t('app', 'Messages') . '<span id="mess" class="badge">0</span>', 'url' => ['message/index']];
				if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4||Yii::$app->session->get('user.ustatus')==5||Yii::$app->session->get('user.ustatus')==6){
					$items[] = ['label' => '<span class="fa fa-phone" aria-hidden="true"></span> ' . Yii::t('app', 'Calls'), 'url' => ['call/index']];
					$items[] = ['label' => '<span class="fa fa-graduation-cap" aria-hidden="true"></span> ' . Yii::t('app', 'Clients'), 'url' => ['studname/index']];
				}
				if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4||Yii::$app->session->get('user.ustatus')==6||Yii::$app->session->get('user.ustatus')==8||Yii::$app->session->get('user.uteacher')) {
					$items[] = ['label' => '<span class="fa fa-suitcase" aria-hidden="true"></span> ' . Yii::t('app', 'Teachers'), 'url' => ['teacher/index']];
				}
				if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
					$items[] = ['label' => '<span class="fa fa-shopping-cart" aria-hidden="true"></span> ' . Yii::t('app', 'Services'), 'url' => ['service/index']];
					$items[] = ['label' => '<span class="fa fa-gift" aria-hidden="true"></span> ' . Yii::t('app', 'Sales'), 'url' => ['sale/index']];
				}
				if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.uid')==146||Yii::$app->session->get('user.ustatus')==9){
					$items[] = ['label' => '<span class="fa fa-retweet" aria-hidden="true"></span> ' . Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
				}
				if(Yii::$app->session->get('user.ustatus')==3) {
					$items[] = ['label' => '<span class="fa fa-user" aria-hidden="true"></span> ' . Yii::t('app', 'Users'), 'url' => ['user/index']];
				}
				if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4||Yii::$app->session->get('user.uid')==146||Yii::$app->session->get('user.uid')==211){
					$items[] = ['label' => '<span class="fa fa-book" aria-hidden="true"></span> ' . Yii::t('app', 'References'), 'url' => ['reference/phonebook']];
				}
				$items[] = ['label' => '<span class="fa fa-file" aria-hidden="true"></span> ' . Yii::t('app', 'Orders'), 'url' => ['order/index']];
				$items[] = ['label' => '<span class="fa fa-sign-out" aria-hidden="true"></span> ' . Yii::t('app', 'Logout'), 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']];

    		    echo Nav::widget([
       		        'options'=>['class'=>'nav nav-pills nav-stacked small'],
					'encodeLabels' => false,
                            //'submenuTemplate' => '<ul class="dropdown-menu">{items}</ul>',
    		        'items'=>$items,
    		        ]);
    		?>
    		<p></p>
    	</div><!-- sidebar -->
        <div id="content" <?php echo $class; ?>>
		    <div class="row">
			    <p class="pull-left visible-xs">
                    <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
                </p>
			</div>
            <?= $content ?>
        </div><!-- content-->
    </div>
</div><!--row-->
<?php $this->endContent();
