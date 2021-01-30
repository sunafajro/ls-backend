<?php

namespace app\models;

use app\models\queries\EducationLevelQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "calc_edulevel".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 */
class EducationLevel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'calc_edulevel';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['name'], 'string'],
            [['visible'], 'integer'],
            [['name', 'visible'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }

    /**
     * @return EducationLevelQuery|ActiveQuery
     */
    public static function find(): ActiveQuery
    {
        return new EducationLevelQuery(get_called_class(), []);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }
}
