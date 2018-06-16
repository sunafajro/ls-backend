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
            'id' => 'ID',
            'name' => 'Name',
            'author' => 'Author',
            'isbn' => 'Isbn',
            'description' => 'Description',
            'user' => 'User',
            'data' => 'Data',
            'visible' => 'Visible',
            'calc_bookpublisher' => 'Calc Bookpublisher',
            'calc_lang' => 'Calc Lang',
        ];
    }
}
