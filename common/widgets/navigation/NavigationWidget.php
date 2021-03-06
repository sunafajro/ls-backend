<?php

namespace common\widgets\navigation;

use common\models\BaseNavigation;
use yii\base\Widget;

/**
 * Class NavigationWidget
 * @package common\widgets\navigation
 *
 * @property $model
 * @property bool $hideModal
 * @property string $viewFile
 */
class NavigationWidget extends Widget {
    /** @var BaseNavigation|null  */
    public $model = null;
    /** @var bool $hideModal */
    public $hideModal = false;
    /** @var string $viewFile */
    public $viewFile = 'navigation';

    public function run() {
        NavigationWidgetAsset::register($this->view);
        $data = $this->model->getItems() ?? [];

        return $this->render($this->viewFile, [
            'items'     => $data['navElements'] ?? [],
            'message'   => $data['message'] ?? [],
            'hideModal' => $this->hideModal,
        ]);
    }
}