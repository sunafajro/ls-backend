<?php

namespace app\widgets\navigation;

use app\models\Message;
use app\models\Navigation;
use app\models\Salestud;
use yii\base\Widget;

class NavigationWidget extends Widget {
    const LIMIT_TIME = 15;
    const LOGOUT_URL = '/site/login';

    public function run() {
        NavigationWidgetAsset::register($this->view);
        return $this->render('navigation', [
            'items'   => Navigation::getItems(),
            'message' => Message::getMessagesCount(),
            'sale'    => Salestud::getLastUnapprovedSale(),
        ]);
    }
}