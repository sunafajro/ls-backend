<?php

namespace app\widgets\userInfo;

use app\modules\school\models\Auth;
use app\modules\school\models\User;
use Yii;
use yii\base\Widget;

/**
 * Class UserInfoWidget
 * @package app\widgets\userInfo
 *
 * @property int $userId
 */
class UserInfoWidget extends Widget
{
    /** @var int */
    public $userId;

    /** {@inheritDoc} */
    public function run()
    {
        if ($this->userId) {
            $user = Auth::findIdentity($this->userId);
        } else {
            $user = Yii::$app->user->identity;
        }
        return $this->render('userInfo', [
            'user' => $user,
        ]);
    }
}