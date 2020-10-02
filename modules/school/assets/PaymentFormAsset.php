<?php

namespace app\modules\school\assets;

use Yii;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class PaymentFormAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/school/views/moneystud/assets';

    public $css = [];

    public $js = [];

    public $depends = [
        JqueryAsset::class,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->js[] = 'js/commonForm.js';
        if ((int)Yii::$app->session->get('user.ustatus') === 11) {
            $this->js[] = 'js/payManagerForm.js';
        }
    }
}