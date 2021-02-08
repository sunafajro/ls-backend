<?php

namespace common\models;

use common\models\queries\TeacherQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class Teacher
 * @package common\models
 *
 * @property integer $id
 * @property string $name
 * @property string $birthdate
 * @property string $phone
 * @property string $address
 * @property integer $visible
 * @property double $value_corp       @deprecated надбавка на корпоративные группы
 * @property double $accrual          сумма начислений
 * @property double $fund             @deprecated сумма в фонде
 * @property string $email
 * @property string $social_link      ссылка на профиль в соцсети
 * @property integer $old             [0 - с нами, 1 - не с нами]
 * @property string $description
 * @property integer $employment_type [1 - внештатник, 2 - по трудовому договору]
 * @property integer $company         [1 - школа "Язык для успеха", 2 - студия "Жирафик"]
 *
 * @property-read User $user
 */
class Teacher extends ActiveRecord
{
    const TYPE_FREELANCER     = 1;
    const TYPE_LABOUR_CONTACT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%teachers}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['visible', 'company'], 'default', 'value' => 1],
            [['old', 'value_corp', 'fund'], 'default', 'value' => 0],
            [['name', 'phone', 'email', 'address', 'social_link', 'description'], 'string'],
            [['birthdate'], 'date', 'format' => 'yyyy-mm-dd'],
            [['visible', 'old', 'employment_type'], 'integer'],
            [['value_corp', 'accrual', 'fund'], 'number'],
            [['name', 'employment_type'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Full name'),
            'birthdate'=> Yii::t('app','Birthdate'),
            'phone' => Yii::t('app', 'Phone'),
            'address' => Yii::t('app', 'Address'),
            'visible' => Yii::t('app', 'Visible'),
            'value_corp' => Yii::t('app', 'Corp value'),
            'accrual' => Yii::t('app', 'Accrual sum'),
            'fund' => Yii::t('app', 'Fund sum'),
            'email' => Yii::t('app', 'Email'),
            'social_link' => Yii::t('app', 'Social'),
            'old' => Yii::t('app', 'Status'),
            'description' => Yii::t('app', 'Annotation'),
            'employment_type' => Yii::t('app', 'Employment type'),
            'company' => Yii::t('app', 'Company'),
        ];
    }

    /**
     * @return TeacherQuery
     */
    public static function find(): TeacherQuery
    {
        return new TeacherQuery(get_called_class(), []);
    }

    /**
     * @return string[]
     */
    public static function getEmploymentTypes(): array
    {
        return [
            static::TYPE_FREELANCER => 'Внештатник',
            static::TYPE_LABOUR_CONTACT => 'По трудовому договору',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['calc_teacher' => 'id']);
    }
}
