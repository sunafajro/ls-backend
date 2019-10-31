<?php

namespace app\widgets\navigation;

use app\models\Navigation;
use yii\base\Widget;
use Yii;

class NavigationWidget extends Widget {
    const LOGOUT_URL = '/site/logout';

    public function run() {
        NavigationWidgetAsset::register($this->view);
        $data = Navigation::getItems();
        return $this->render('navigation', [
            'items'     => $data['navElements'] ?? [],
            'message'   => $data['message'],
            'timeLimit' => Yii::$app->params['timeLimit'] ?? 0,
        ]);
    }
}