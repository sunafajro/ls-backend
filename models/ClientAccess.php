<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_client_access".
 *
 * @property integer $id
 * @property integer $site
 * @property string $username
 * @property string $password
 * @property integer $calc_studname
 * @property string $date
 */
class ClientAccess extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_client_access';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site', 'calc_studname'], 'integer'],
            [['username', 'password', 'calc_studname', 'date'], 'required'],
			[['username'], 'unique'],
			[['username'], 'string', 'min' => 3],
			[['password'], 'string', 'min' => 8],
            [['username', 'password'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site' => 'Site',
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'calc_studname' => Yii::t('app', 'Student'),
            'date' => 'Date',
        ];
    }
    /**
     *  метод находит данные по личным кабинетам одинаковых уч. записей и оставляет только одну
     */
    public static function mergeClientAccounts($id1, $id2)
    {
        $client1 = self::findOne($id1);
        $client2 = self::findOne($id2);
        if($client1 !== NULL && $client2 !== NULL) {
            $client2->delete();
            return true;
        }
        elseif($client1 === NULL && $client2 !== NULL) {
            return self::changeStudentId($id1, $id2);
        }
        else {
            return false;
        }
    }
    /**
     *  метод подменяет в строках идентификатор одного студента на идентификатор другого
     */
    protected static function changeStudentId($id1, $id2)
    {
        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update(self::tableName(), ['calc_studname' => $id1], ['calc_studname' => $id2])
        ->execute();

        return ($sql == 0) ? false : true;
    }
}
