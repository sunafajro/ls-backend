<?php

namespace school\widgets\userInfo;

use school\models\Auth;
use school\models\User;
use Yii;
use yii\base\Widget;

/**
 * Class UserInfoWidget
 * @package school\widgets\userInfo
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