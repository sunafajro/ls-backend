<?php


namespace app\modules\exams\models\forms;

use app\models\forms\BaseUserForm;
use app\modules\exams\models\User;

class UserForm extends BaseUserForm
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritDoc}
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'login', 'pass', 'pass_repeat', 'status'];
        $scenarios[self::SCENARIO_UPDATE] = ['name', 'login', 'status'];

        return $scenarios;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                ['login'], function ($attribute) {
                    \Yii::error(['user: ' => $this->_model]);
                    if (!empty($this->_model)) {
                        $users = User::find()->byLogin($this->login)->byExceptIds([$this->_model->id])->all();
                        if (!empty($users)) {
                            $this->addError($attribute, 'Такой логин пользователя уже существует в системе.');
                        }
                    } else {
                        if (User::find()->byLogin($this->login)->exists()) {
                            $this->addError($attribute, 'Такой логин пользователя уже существует в системе.');
                        }
                    }
                }
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function save()
    {
        if ($this->validate()) {
            $user = $this->_model ? clone($this->_model) : null;
            if (empty($user)) {
                $user = new User();
            }
            $attributes = $this->getAttributes();
            unset($attributes['pass_repeat']);
            $user->setAttributes($attributes);
            if (!empty($this->_model)) {
                $user->pass = $this->_model->pass;
            }
            if (!$user->save()) {
                foreach ($user->getErrors() as $attribute => $errors) {
                    if (is_string($errors)) {
                        $this->addError($attribute, $errors);
                    }
                }

                return false;
            } else {
                if (!$this->id) {
                    $this->id = $user->id;
                }
                $this->_model = $user;

                return true;
            }
        } else {
            return false;
        }
    }
}