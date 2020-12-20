<?php


namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "spend_successes".
 *
 * @property integer $id
 * @property integer $visible
 * @property integer $student_id
 * @property integer $count
 * @property string  $cause
 * @property integer $user_id
 * @property string  $created_at
 */

class SpendSuccesses extends ActiveRecord
{
    use StudentMergeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName() : string
    {
        return 'spend_successes';
    }

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [['visible'],    'default', 'value' => 1],
            [['user_id'],    'default', 'value' => Yii::$app->user->identity->id ?? 0],
            [['created_at'], 'default', 'value' => date('Y-m-d')],
            [['count', 'student_id', 'user_id', 'visible'], 'integer'],
            [['count'], 'validateCount'],
            [['cause'], 'string'],
            [['created_at'], 'safe'],
            [['count', 'cause', 'student_id', 'user_id', 'visible', 'created_at'], 'required'],
        ];
    }

    public function validateCount($attribute, $params, $validator)
    {
        /** @var Student $student */
        $student = Student::find()->andWhere(['id' => $this->student_id])->one();
        if ($student) {
            if ($student->getSuccessesCount() < $this->$attribute) {
                $this->addError($attribute, 'Невозжно списать "успешиков" больше, чем на балансе у клиента.');
            }
        }
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels() : array
    {
        return [
            'created_at' => 'Когда списано',
            'count' => 'Количество',
            'cause' => 'Причина/цель списания',
            'user_id' => 'Кем списано',
        ];
    }

    public function attributeLabel(string $str) : string
    {
        $labels = $this->attributeLabels();

        return $labels[$str] ?? $str;
    }
}