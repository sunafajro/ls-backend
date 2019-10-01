<?php


namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "book_publishers".
 *
 * @property integer $id
 * @property string  $name
 */

class BookPublisher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book_publishers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['visible'], 'integer'],
            [['visible'], 'default', 'value'=> 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'name' => Yii::t('app', 'Name'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save();
    }

    public static function getPublishers() : array
    {
        return self::find()->andWhere(['visible' => 1])->asArray()->all() ?? [];
    }

    public static function getPublishersSimple() : array
    {
        return ArrayHelper::map(self::getPublishers() ?? [], 'id', 'name');
    }
}