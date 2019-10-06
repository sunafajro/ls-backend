<?php


namespace app\models;

use Yii;

/**
 * This is the model class for table "book_orders".
 *
 * @property integer $id
 * @property string  $date_start
 * @property string  $date_end
 * @property int     $count
 * @property float   $purchase_cost
 * @property float   $selling_cost
 * @property string  $status
 * @property integer $user_id
 * @property string  $created_at
 * @property integer $visible
 * 
 * @property BookOrderPosition[] $positions
 * @property User                $user
 */

class BookOrder extends \yii\db\ActiveRecord
{
    const STATUS_OPENED = 'opened';
    const STATUS_CLOSED = 'closed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_start', 'date_end'], 'required'],
            [['count', 'user_id', 'visible'], 'integer'],
            [['purchase_cost', 'selling_cost'], 'number'],
            [['status'],     'default', 'value'=> self::STATUS_OPENED],
            [['user_id'],    'default', 'value'=> Yii::$app->user->identity->id],
            [['created_at'], 'default', 'value'=> date('Y-m-d')],
            [['visible'],    'default', 'value'=> 1],
            [['date_start', 'date_end', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => '№',
            'date_start'    => Yii::t('app', 'Start date'),
            'date_end'      => Yii::t('app', 'End date'),
            'count'         => Yii::t('app', 'Count'),
            'purchase_cost' => Yii::t('app', 'Purchase cost'),
            'selling_cost'  => Yii::t('app', 'Selling cost'),
            'status'        => Yii::t('app', 'Status'),
            'user_id'       => Yii::t('app', 'User ID'),
            'created_at'    => Yii::t('app', 'Created at'),
            'visible'       => Yii::t('app', 'Active'),
        ];
    }

    public function restore()
    {
        $this->visible = 1;
        return $this->save();
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save();
    }

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_OPENED => Yii::t('app', 'Opened'),
            self::STATUS_CLOSED => Yii::t('app', 'Closed'),
        ];
    }

    public static function getStatusLabel(string $key) : string
    {
        $statuses = self::getStatusLabels();
        return $statuses[key] ?? '';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function getPositions()
    {
        return $this->hasMany(BookOrderPosition::class, ['book_order_id' => 'id']);
    }
}