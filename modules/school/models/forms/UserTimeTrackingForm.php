<?php


namespace app\modules\school\models\forms;

use app\modules\school\models\UserTimeTracking;
use yii\base\Model;

/**
 * Class UserTimeTrackingForm
 * @package app\modules\school\models\forms
 *
 * @property string $type
 * @property string $start
 * @property string $end
 * @property string $comment
 * @property int    $userId
 */
class UserTimeTrackingForm extends Model
{
    /** @var string */
    public $type;
    /** @var string */
    public $start;
    /** @var string */
    public $end;
    /** @var string */
    public $comment;

    /** @var int */
    public $userId;

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            [['type', 'comment', 'start', 'end'], 'string'],
            [['type'], 'in', 'range' => array_keys(UserTimeTracking::getTypeLabels())],
            [['type', 'start', 'end'], 'required'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels() : array
    {
        return [
            'start'      => 'Начало периода',
            'end'        => 'Конец периода',
            'type'       => 'Тип',
            'comment'    => 'Комментарий',
        ];
    }

    /**
     * @return bool
     */
    public function save() : bool
    {
        if (!$this->validate()) {
            return false;
        }

        $userTimeTracking = new UserTimeTracking([
            'entity_id' => $this->userId,
        ]);
        $start = \DateTime::createFromFormat('d.m.Y H:i', $this->start);
        $end   = \DateTime::createFromFormat('d.m.Y H:i', $this->end);
        if ($start === false) {
            $this->addError('start', 'Значение "Начало" указанно не верно.');
        } else {
            $userTimeTracking->start = $start->format('Y-m-d H:i:s');
        }
        if ($end === false) {
            $this->addError('end', 'Значение "Начало" указанно не верно.');
        } else {
            $userTimeTracking->end = $end->format('Y-m-d H:i:s');
        }
        if ($this->hasErrors()) {
            return false;
        }

        $userTimeTracking->type    = $this->type;
        $userTimeTracking->comment = $this->comment;

        if (!$userTimeTracking->save()) {
            foreach ($userTimeTracking->getErrors() as $attribute => $errors) {
                if (property_exists($this, $attribute)) {
                    $this->addError($attribute, reset($errors));
                }
            }
            \Yii::error($userTimeTracking->getErrors());
            return false;
        }

        return true;
    }
}