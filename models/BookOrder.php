<?php


namespace app\models;

use Yii;

/**
 * This is the model class for table "book_orders".
 *
 * @property integer $id
 * @property string  $date_start
 * @property string  $date_end
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
            [['user_id', 'visible'], 'integer'],
            [['status'],        'default', 'value' => self::STATUS_OPENED],
            [['user_id'],       'default', 'value' => Yii::$app->user->identity->id],
            [['created_at'],    'default', 'value' => date('Y-m-d')],
            [['visible'],       'default', 'value' => 1],
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
            'status'        => Yii::t('app', 'Status'),
            'user_id'       => Yii::t('app', 'User ID'),
            'created_at'    => Yii::t('app', 'Created at'),
            'visible'       => Yii::t('app', 'Active'),
        ];
    }

    public function close()
    {
        $this->status = self::STATUS_CLOSED;
        return $this->save(true, ['status']);
    }

    public function open()
    {
        $this->status = self::STATUS_OPENED;
        return $this->save(true, ['status']);
    }

    public function restore()
    {
        $this->visible = 1;
        return $this->save(true, ['visible']);
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_OPENED => Yii::t('app', 'Opened'),
            self::STATUS_CLOSED => Yii::t('app', 'Closed'),
        ];
    }

    public static function getStatusLabel(string $key): string
    {
        $statuses = self::getStatusLabels();
        return $statuses[$key] ?? '';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPositions()
    {
        return $this->hasMany(BookOrderPosition::class, ['book_order_id' => 'id'])
            ->andWhere([BookOrderPosition::tableName() . '.visible' => 1])
            ->andFilterWhere(['office_id' => (int) Yii::$app->session->get('user.ustatus') === 4
                ? Yii::$app->session->get('user.uoffice_id')
                : null]);
    }

    public function getPositionsCount()
    {
        return $this->getPositions()->count();
    }

    public function getOrderCounters(int $office_id = null) : array
    {
        $bct  = BookCost::tableName();
        $bopt = BookOrderPosition::tableName();
        return $this->getPositions()
            ->select([
                'total_count'         => "SUM({$bopt}.count)",
                'total_purchase_cost' => "SUM({$bopt}.count * {$bct}.cost)",
                'total_selling_cost'  => "SUM({$bopt}.paid)",
            ])
            ->innerJoin($bct, "{$bct}.id = {$bopt}.purchase_cost_id")
            ->andFilterWhere(['office_id' => $office_id])
            ->asArray()
            ->one();
    }

    public static function getCurrentOrder()
    {
        return self::find()->andWhere([
            'visible' => 1,
            'status'  => self::STATUS_OPENED,
        ])->one();
    }
}
