<?php

/* @var $this View
 * @var array $phones
 * @var array $languages
 * @var array $schoolbooks
 */

use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\web\View;


$this->title = 'Система учета :: '.Yii::t('app','References');
$this->params['breadcrumbs'][] = Yii::t('app','References');

switch(Yii::$app->request->get('type')){
    case 1: $viewsfile = 'phones'; break;
    case 2: $viewsfile = 'languages'; break;
    case 7: $viewsfile = 'cities'; break;
    case 8: $viewsfile = 'offices'; break;
    case 9: $viewsfile = 'rooms'; break;
    case 12: $viewsfile = 'schoolbooks'; break;
    default: $viewsfile = 'phones';
}
?>
<div class="site-about">
<?php
    $items[] = ['label' => Yii::t('app','Phones'), 'url' => ['site/reference','type'=>1]];
    if(Yii::$app->session->get('user.ustatus')==3) {
        $items[] = ['label' => Yii::t('app','Languages'), 'url' => ['site/reference','type'=>2]];
        $items[] = ['label' => 'Pay norms', 'items' => [
			['label' => Yii::t('app','Student pay norms'), 'url' => ['site/reference','type'=>3]],
			['label' => Yii::t('app','Teacher pay norms'), 'url' => ['site/reference','type'=>4]],
		]];
        $items[] = ['label' => Yii::t('app','Time norms'), 'url' => ['site/reference','type'=>5]];
        $items[] = ['label' => Yii::t('app','Knowledge levels'), 'url' => ['site/reference','type'=>6]];
        $items[] = ['label' => Yii::t('app', 'Locations'), 'items' => [
            ['label' => Yii::t('app','Cities'), 'url' => ['site/reference','type'=>7]],
            ['label' => Yii::t('app','Offices'), 'url' => ['site/reference','type'=>8]],
            ['label' => Yii::t('app','Rooms'), 'url' => ['site/reference','type'=>9]],
        ]];
        $items[] = ['label' => Yii::t('app','Wises'), 'url' => ['site/reference','type'=>10]];
        $items[] = ['label' => Yii::t('app','Attract ways'), 'url' => ['site/reference','type'=>11]];
        $items[] = ['label' => Yii::t('app','Books'), 'url' => ['site/reference','type'=>12]];
    }
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

    <?= $this->render($viewsfile, [
        'phones' => $phones,
        'languages' => $languages,
        'schoolbooks' => $schoolbooks,
    ]) ?>

</div>
