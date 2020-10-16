<?php

namespace app\models;

use app\models\Sale;
use app\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_salestud".
 *
 * @property integer $id
 * @property integer $calc_studname
 * @property integer $calc_sale
 * @property integer $user
 * @property string  $data
 * @property integer $visible
 * @property string  $data_visible
 * @property integer $user_visible
 * @property string  $data_used
 * @property integer $user_used
 * @property integer $approved
 * @property string  $reason
 */
class Salestud extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_salestud';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_studname', 'calc_sale'], 'required'],
            [['reason'], 'string'],
            [['calc_studname', 'calc_sale', 'user', 'visible', 'user_visible', 'user_used', 'approved'], 'integer'],
            [['data', 'data_visible', 'data_used'], 'safe'],
            [['visible'],      'default', 'value' => 1],
            [['user'],         'default', 'value' => Yii::$app->user->identity->id ?? 0],
            [['data'],         'default', 'value' => date('Y-m-d')],
            [['user_visible'], 'default', 'value' => 0],
            [['data_visible'], 'default', 'value' => '0000-00-00'],
            [['user_used'],    'default', 'value' => 0],
            [['data_used'],    'default', 'value' => '0000-00-00'],
            [['approved'],     'default', 'value' => 0],
            [['reason'],       'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('app', 'ID'),
            'calc_studname' => Yii::t('app', 'Calc Studname'),
            'calc_sale'     => Yii::t('app', 'Sale'),
            'user'          => Yii::t('app', 'User'),
            'data'          => Yii::t('app', 'Data'),
            'visible'       => Yii::t('app', 'Visible'),
            'data_visible'  => Yii::t('app', 'Data Visible'),
            'user_visible'  => Yii::t('app', 'User Visible'),
            'data_used'     => Yii::t('app', 'Data Used'),
            'user_used'     => Yii::t('app', 'User Used'),
            'approved'      => Yii::t('app', 'Approved'),
            'reason'        => Yii::t('app', 'Reason'),
        ];
    }
    
    /**
     * @return bool
     */
    public function delete() : bool
    {
        $this->visible = 0;
        $this->user_visible = Yii::$app->user->identity->id;
        $this->data_visible = date('Y-m-d');
        $this->approved = 0;

        return $this->save(true, ['visible', 'user_visible', 'data_visible', 'approved']);
    }

    /**
     * @param string|null $reason
     * 
     * @return bool
     */
    public function restore(string $reason = null) : bool
    {
        $this->visible = 1;
        $this->user         = Yii::$app->session->get('user.uid');
        $this->data         = date('Y-m-d');
        $this->user_visible = 0;
        $this->data_visible = '0000-00-00';
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $this->approved = 1;
        }
        $this->reason = $reason;

        return $this->save(true, ['visible', 'user', 'data', 'user_visible', 'data_visible', 'approved', 'reason']);
    }

    /**
     * @return bool
     */
    public function approve() : bool
    {
        $this->approved = 1;

        return $this->save(true, ['approved']);
    }

    /**
     * возвращает количество неподтвержденных скидок для панели навигации
     * 
     * @return int
     */
    public static function getSalesCount() : int
    {
        if ((int)Yii::$app->session->get('user.ustatus') === 3 ||
            (int)Yii::$app->session->get('user.uid') === 389) {
            $id = NULL;
        } else if((int)Yii::$app->session->get('user.ustatus') === 4) {
            $id = (int)Yii::$app->session->get('user.uid');
        } else {
            return null;
        }

        $sales = (new \yii\db\Query())
        ->select('count(id) as cnt')
        ->from(['ss' => self::tableName()])
        ->where([
            'ss.approved' => 0,
            'ss.visible'  => 1,
        ])
        ->andFilterWhere(['ss.user' => $id])
        ->one();

        return (!empty($sales)) ? $sales['cnt'] : 0;
    }

    /** 
     * возвращает одну неподтвержденную скидку для модального окна
     * @return array|null
     */
    public static function getLastUnapprovedSale()
    {
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {

            $sale = (new \yii\db\Query())
            ->select([
                'sid'        => 'ss.id',
                'clientId'   => 'ss.calc_studname',
                'clientName' => 'sn.name',
                'saleId'     => 'ss.calc_sale',
                'saleName'   => 's.name',
                'user'       => 'u.name'
            ])
            ->from(['ss'      => self::tableName()])
            ->innerJoin(['sn' => Student::tableName()], 'sn.id = ss.calc_studname')
            ->innerJoin(['s'  => Sale::tableName()],    's.id = ss.calc_sale')
            ->innerJoin(['u'  => 'user'],    'u.id = ss.user')
            ->where([
                'ss.approved' => 0,
                'ss.visible'  => 1
            ])
            ->one();

            if (!empty($sale)) {
                $sale['title'] = 'Подтвердить скидку для клиента';
            }

            return (!empty($sale)) ? $sale : null;
        } else {
            return null;
        }        
    }

    /** 
     * возвращает список всех назначенных клиенту скидок 
     * используется в StudnameController.php actionView
     * @param int $sid
     * 
     * @return array
     */
    public static function getAllClientSales($sid) : array
    {
        return (new \yii\db\Query())
        ->select('ss.id as id, s.name as name, u.name as user, ss.data as date, uu.name as usedby, ss.data_used as usedate, uv.name as remover, ss.data_visible as deldate, ss.visible as visible, ss.approved as approved')
        ->from(['ss'      => self::tableName()])
        ->leftJoin(['s'   => Sale::tableName()], 's.id = ss.calc_sale')
        ->leftJoin(['u'   => 'user'], 'u.id = ss.user')
        ->leftJoin(['uu'  => 'user'], 'uu.id = ss.user_used')
        ->leftJoin(['uv'  => 'user'], 'uv.id = ss.user_visible')
        ->where([
            'ss.visible' => 1,
            'ss.calc_studname' => $sid,
        ])
        ->orderby(['ss.id' => SORT_DESC])
        ->all();
    }

    /** 
     * возвращает список активных скидок клиента
     * @param int $sid
     * 
     * @return array
     */
    public static function getClientSales($sid) : array
    {
        return (new \yii\db\Query())
        ->select('ss.id as id, s.name as name, s.procent as procent, s.value as value')
        ->from(['ss'    => self::tableName()])
        ->leftJoin(['s' => Sale::tableName()], 's.id = ss.calc_sale')
        ->where([
            'ss.calc_studname' => $sid,
            'ss.visible'       => 1
        ])
        ->all();
    }    

    /** 
     * возвращает список всех назначенных клиенту скидок
     * @param int $sid
     * 
     * @return array
     */
    public static function getClientSalesSplited(int $sid) : array
    {
        $sales = static::getClientSales($sid);
         
        $rubsale = [];
        $procsale = [];

        /* раскидываем скидки по массивам */
        foreach($sales as $s){
            if ((int)$s['procent'] === 0) {
                $rubsale[] = $s;
            } else if ((int)$s['procent'] === 1) {
                $procsale[] = $s;
            }
        }

        return [
            'rub'  => (!empty($rubsale) ? $rubsale : []),
            'proc' => (!empty($procsale) ? $procsale : [])
        ];        
    }

    /**
     * возвращает массив из списков назначенных клиенту ссылок 
     * 0 - рублевые, 1 - процентные, 2 - постоянные
     * @param int $sid
     * 
     * @return array
     */
    public static function getClientSalesSimple(int $sid) : array
    {
        $sales = self::getClientSales($sid);

        $rubsale = [];
        $procsale = [];
        
        /* раскидываем скидки по массивам */
        foreach($sales as $s){
            if((int)$s['procent'] === 0){
                $rubsale[$s['id']] = $s['name'];
            } else if((int)$s['procent'] === 1) {
                $procsale[$s['id']] = $s['name'];
            }
        }

        return [
            'rub'  => (!empty($rubsale) ? $rubsale : NULL),
            'proc' => (!empty($procsale) ? $procsale : NULL)
        ];
    }

    /**
     * определяет и возвращает постоянную скидку клиента
     * вызывается из StudnameController.php actionView, ?
     * @param int $sid
     * 
     * @return array|null
     */
    public static function getClientPermamentSale(int $sid)
    {
        $tmp_moneysum = (new \yii\db\Query())
        ->select('sum(m.value) as money')
        ->from(['m' => Moneystud::tableName()])
        ->where([
            'm.visible'       => 1,
            'm.calc_studname' => $sid
        ])
        ->one();

        $permsale = (new \yii\db\Query())
        ->select('s.id as id, s.name as name, s.value as value')
        ->from(['s' => Sale::tableName()])
        ->where([
            's.visible' => 1,
            's.procent' => 2
        ])
        ->andWhere(['<=', 's.base', (int)$tmp_moneysum['money']])
        ->orderby(['s.id' => SORT_DESC])
        ->one();

        return !empty($permsale) ? $permsale : NULL;
    }

    /**
     * находим или создаем рублевую скидку и привязываем ее к студенту
     * @param float  $value
     * @param int    $sid
     * @param string $purpose
     * 
     * @return int
     */
    public static function applyRubSale(float $value, int $sid, string $purpose = 'Коррекция стоимости счета') : int
    {
        $rubsale = (new \yii\db\Query())
        ->select('s.id as id')
        ->from(['s' => Sale::tableName()])
        ->where([
            's.visible' => 1,
            's.procent' => 0,
            'value'     => $value, 
        ])
        ->one();

        if (!empty($rubsale)) {
            /** @var Salestud $salestud */
            $salestud = Salestud::find()->where(['calc_studname' => $sid, 'calc_sale' => $rubsale['id']])->one();
            if (!empty($salestud)) {
                if ($salestud->restore($purpose)) {
                    return $salestud->id;
                } else {
                    return 0;
                }
            } else {
                return static::addSaleToStudent($sid, $rubsale['id'], $purpose);    
            }            
        } else {
            /* создаем новую рублевую скидку и привязываем ее к клиенту */
            $sale = Sale::createSale(
                $value > 0 ? "Скидка корректирующая $value р." : ('Надбавка корректирующая +' . abs($value) . ' р.'),
                0,
                $value,
                0
            );
            if ($sale > 0) {
                return static::addSaleToStudent($sid, $sale, $purpose);
            } else {
                return 0;
            }
        }
    }

    /**
     * привязываем рублевую скидку к студенту
     * @param int    $student
     * @param int    $sale
     * @param string $purpose
     * 
     * @return int
     */
    public static function addSaleToStudent(int $student, int $sale, string $purpose) : int
    {
        /* привязываем скидку к клиенту */
        $salestud = new Salestud();
        $salestud->calc_studname = $student;
        $salestud->calc_sale     = $sale;
        $salestud->reason        = $purpose;
        /* если скидка назначается руководителем, сразу подтверждаем */
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $salestud->approved = 1;
        }

        if ($salestud->save()) {
            return $salestud->id;
        } else {
            return 0;    
        }
    }

    /**
     * @deprecated
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
     * @param int @id1
     * @param int @id2
     * 
     * @return bool
     */
    public static function changeStudentId(int $id1, int $id2) : bool
    {
        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update(self::tableName(), ['calc_studname' => $id1], ['calc_studname' => $id2])
        ->execute();

        return ($sql == 0) ? false : true;
    }
}
