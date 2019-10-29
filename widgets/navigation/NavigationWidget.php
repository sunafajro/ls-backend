<?php

namespace app\widgets\navigation;

use app\models\Navigation;
use yii\base\Widget;

class NavigationWidget extends Widget {
    const LIMIT_TIME = 15;
    const LOGOUT_URL = '/site/logout';

    public function run() {
        NavigationWidgetAsset::register($this->view);
        $data = Navigation::getItems();
        return $this->render('navigation', [
            'items'   => $data['navElements'] ?? [],
            'message' => $data['message'],
        ]);
    }
}