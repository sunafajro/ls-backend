<?php


namespace school\widgets\sidebarButton;

/**
 * Class SidebarButtonWidget
 * @package school\widgets\sidebarButton
 */
class SidebarButtonWidget extends \yii\base\Widget
{
    /** {@inheritDoc} */
    public function run()
    {
        return $this->render('view');
    }
}