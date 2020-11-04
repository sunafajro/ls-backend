<?php

namespace app\models\forms;

use app\models\BaseUser;
use yii\base\Model;

class BaseUserForm extends Model
{
    /** @var int */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $login;
    /** @var string */
    public $pass;
    /** @var string */
    public $pass_repeat;
    /** @var int */
    public $status;

    /** @var BaseUser */
    protected $_model;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['name', 'login', 'pass', 'pass_repeat'], 'trim'],
            [['name', 'login', 'pass', 'pass_repeat'], 'string'],
            [['status'], 'integer'],
            [['login', 'name'], 'string', 'min' => 3],
            [['pass', 'pass_repeat'], 'string', 'min' => 8],
            [['pass', 'pass_repeat'], function ($attribute) {
                if ($this->pass !== $this->pass_repeat) {
                    $this->addError($attribute, 'Пароль и повтор пароля не совпадают.');
                }
            }],
            [['name', 'login', 'pass', 'pass_repeat', 'status'], 'required'],
        ];

    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'name'        => 'ФИО',
            'login'       => 'Логин',
            'pass'        => 'Пароль',
            'pass_repeat' => 'Повтор пароля',
            'status'      => 'Роль',
        ];
    }

    /**
     * @var BaseUser $user
     *
     * @return BaseUserForm
     */
    public static function loadFromModel($user)
    {
        $form = new static();
        $form->setAttributes($user->getAttributes());
        $form->pass        = '';
        $form->pass_repeat = '';
        $form->_model      = $user;

        return $form;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }
}