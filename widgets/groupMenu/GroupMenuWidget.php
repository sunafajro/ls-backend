<?php

namespace app\widgets\groupMenu;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class GroupMenuWidget
 * @package app\widgets\groupMenu
 *
 * @property string $activeItem
 * @property bool   $canCreate
 * @property int    $groupId
 */
class GroupMenuWidget extends Widget {
    /** @var string */
    public $activeItem = 'journal';
    /** @var bool  */
    public $canCreate = false;
    /** @var int  */
    public $groupId;

    /** {@inheritDoc} */
    public function run() {
        $menuItems = [];
        if ($this->canCreate) {
            $menuItems[] = [
                'name'     => Yii::t('app','Add lesson'),
                'icon'     => 'plus',
                'url'      => Url::to(['journalgroup/create', 'gid' => $this->groupId]),
                'isActive' => $this->activeItem === 'add-lesson',
            ];
        }
        $menuItems[] = [
            'name'     => Yii::t('app','Journal'),
            'icon'     => 'list',
            'url'      => Url::to(['groupteacher/view', 'id' => $this->groupId]),
            'isActive' => $this->activeItem === 'journal',
        ];
        $menuItems[] = [
            'name'     => Yii::t('app','Students'),
            'icon'     => 'graduation-cap',
            'url'      => Url::to(['groupteacher/addstudent', 'gid' => $this->groupId]),
            'isActive' => $this->activeItem === 'students',
        ];
        $menuItems[] = [
            'name'     => Yii::t('app','Teachers'),
            'icon'     => 'suitcase',
            'url'      => Url::to(['groupteacher/addteacher', 'gid' => $this->groupId]),
            'isActive' => $this->activeItem === 'teachers',
        ];
        $menuItems[] = [
            'name'     => Yii::t('app','Books'),
            'icon'     => 'book',
            'url'      => Url::to(['group-book/create', 'gid' => $this->groupId]),
            'isActive' => $this->activeItem === 'books',
        ];
        $menuItems[] = [
            'name'     => Yii::t('app','Announcements'),
            'icon'     => 'bullhorn',
            'url'      => Url::to(['groupteacher/announcements', 'id' => $this->groupId]),
            'isActive' => $this->activeItem === 'announcements',
        ];
        $menuItems[] = [
            'name'     => Yii::t('app','Files'),
            'icon'     => 'file',
            'url'      => Url::to(['groupteacher/files', 'id' => $this->groupId]),
            'isActive' => $this->activeItem === 'files',
        ];
        return $this->render('groupMenu', [
            'menuItems' => $menuItems,
        ]);
    }
}