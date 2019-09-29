<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_book".
 *
 * @property integer $id
 * @property string $name
 * @property string $author
 * @property string $isbn
 * @property string $description
 * @property integer $user
 * @property string $data
 * @property integer $visible
 * @property integer $calc_bookpublisher
 * @property integer $calc_lang
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_book';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'author', 'isbn', 'description', 'user', 'data', 'visible', 'calc_bookpublisher', 'calc_lang'], 'required'],
            [['name', 'author', 'isbn', 'description'], 'string'],
            [['user', 'visible', 'calc_bookpublisher', 'calc_lang'], 'integer'],
            [['data'], 'safe']
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
            'author' => Yii::t('app', 'Author'),
            'isbn' => Yii::t('app', 'ISBN'),
            'description' => Yii::t('app', 'Description'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Date'),
            'visible' => 'Visible',
            'calc_bookpublisher' => Yii::t('app', 'Publisher'),
            'calc_lang' => Yii::t('app', 'Language'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save();
    }
}
