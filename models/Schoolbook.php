<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_schoolbook".
 *
 * @property integer $id
 * @property string $name
 * @property string $author
 * @property string $isbn
 * @property integer $visible
 * @property integer $calc_lang
 */
class Schoolbook extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_schoolbook';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible', 'calc_lang'], 'integer'],
            [['name', 'author', 'isbn'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'author' => Yii::t('app', 'Author'),
            'isbn' => Yii::t('app', 'ISBN'),
            'visible' => Yii::t('app', 'Visible'),
            'calc_lang' => Yii::t('app', 'Language'),
        ];
    }
}
