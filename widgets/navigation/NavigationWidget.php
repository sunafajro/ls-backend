<?php

namespace app\widgets\navigation;

use app\models\Navigation;
use yii\base\Widget;

class NavigationWidget extends Widget {
    /** @var Navigation|null  */
    public $model = null;
    /** @var bool $hideModal */
    public $hideModal = false;

    public function run() {
        NavigationWidgetAsset::register($this->view);
        $data = $this->model->getItems() ?? [];
        return $this->render('navigation', [
            'items'     => $data['navElements'] ?? [],
            'message'   => $data['message'] ?? [],
            'hideModal' => $this->hideModal,
        ]);
    }
}