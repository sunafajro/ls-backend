<?php

namespace app\models;

use Yii;
use app\models\Sale;
/**
 * This is the model class for table "calc_salestud".
 *
 * @property integer $id
 * @property integer $calc_studname
 * @property integer $calc_sale
 * @property integer $user
 * @property string $data
 * @property integer $visible
 * @property string $data_visible
 * @property integer $user_visible
 * @property string $data_used
 * @property integer $user_used
 */
class Salestud extends \yii\db\ActiveRecord
{
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
            [['calc_studname', 'calc_sale', 'user', 'data', 'visible'], 'required'],
            [['calc_studname', 'calc_sale', 'user', 'visible', 'user_visible', 'user_used', 'approved'], 'integer'],
            [['data', 'data_visible', 'data_used'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'calc_studname' => Yii::t('app', 'Calc Studname'),
            'calc_sale' => Yii::t('app', 'Sale'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Data'),
            'visible' => Yii::t('app', 'Visible'),
            'data_visible' => Yii::t('app', 'Data Visible'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'data_used' => Yii::t('app', 'Data Used'),
            'user_used' => Yii::t('app', 'User Used'),
            'approved' => Yii::t('app', 'Approved')
        ];
    }
    
    /**
     * возвращает количество неподтвержденных скидок для панели навигации
     * @return integer
     */
    public static function getSalesCount()
    {
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $id = NULL;
        } else if((int)Yii::$app->session->get('user.ustatus') === 4) {
            $id = (int)Yii::$app->session->get('user.uid');
        } else {
            return null;
        }

        $sales = (new \yii\db\Query())
        ->select('count(id) as cnt')
        ->from('calc_salestud ss')
        ->where('ss.approved=:zero and ss.visible=:one', [':one'=>1, ':zero'=> 0])
        ->andFilterWhere(['ss.user' => $id])
        ->one();

        return (!empty($sales)) ? $sales['cnt'] : 0;
    }

    /** 
     * возвращает одну неподтвержденную скидку для модального окна
     * @return array
     */
    public static function getLastUnapprovedSale()
    {
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {

            $sale = (new \yii\db\Query())
            ->select('ss.id as sid, ss.calc_studname as clientId, sn.name as clientName, ss.calc_sale as saleId, s.name as saleName, u.name as user')
            ->from('calc_salestud ss')
            ->innerJoin('calc_studname sn', 'sn.id=ss.calc_studname')
            ->innerJoin('calc_sale s', 's.id=ss.calc_sale')
            ->innerJoin('user u', 'u.id=ss.user')
            ->where('ss.approved=:zero and ss.visible=:one', [':one'=>1, ':zero'=> 0])
            ->one();

            if (!empty($sale)) {
                $sale['title'] = 'Подтвердить скидку для клиента.';
            }

            return (!empty($sale)) ? $sale : null;
        } else {
            return null;
        }        
    }

    /** 
     * возвращает список всех назначенных клиенту скидок 
     * используется в StudnameController.php actionView
     * @param integer $sid
     * @return array
     */
    public static function getAllClientSales($sid)
    {
        $sales = (new \yii\db\Query())
        ->select('ss.id as id, s.name as name, u.name as user, ss.data as date, uu.name as usedby, ss.data_used as usedate, uv.name as remover, ss.data_visible as deldate, ss.visible as visible, ss.approved as approved')
        ->from('calc_salestud ss')
        ->leftJoin('calc_sale s', 's.id=ss.calc_sale')
        ->leftJoin('user u', 'u.id=ss.user')
        ->leftJoin('user uu', 'uu.id=ss.user_used')
        ->leftJoin('user uv' , 'uv.id=ss.user_visible')
        ->where([
            'ss.visible' => 1,
            'ss.calc_studname' => $sid,
        ])
        ->orderby(['ss.id' => SORT_DESC])
        ->all();

        return $sales;
    }

    /** 
     * возвращает список активных скидок клиента
     * @param integer $sid
     * @return array
     */
    public static function getClientSales($sid)
    {
        $sales = (new \yii\db\Query())
        ->select('ss.id as id, s.name as name, s.procent as procent, s.value as value')
        ->from('calc_salestud ss')
        ->leftJoin('calc_sale s', 's.id=ss.calc_sale')
        ->where('ss.calc_studname=:sid AND ss.visible=:one', [':sid'=>$sid, ':one'=>1])
        ->all();
        
        return $sales;

    }    

    /** 
     * возвращает список всех назначенных клиенту скидок
     * @param integer $sid
     * @return array
     */
    public static function getClientSalesSplited($sid)
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
     * @param integer $sid
     * @return array
     */
    public static function getClientSalesSimple($sid)
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
     * @param integer $sid
     * @return array 
     */
    public static function getClientPermamentSale($sid)
    {
        $tmp_moneysum = (new \yii\db\Query())
        ->select('sum(m.value) as money')
        ->from('calc_moneystud m')
        ->where('m.visible=:one AND m.calc_studname=:sid', 
        [':one' => 1, ':sid' => $sid])
        ->one();

        $permsale = (new \yii\db\Query())
        ->select('id as id, name as name, value as value')
        ->from('calc_sale')
        ->where('visible=:one AND base <=:payments and procent=:type', 
        [':one' => 1, ':payments' => (int)$tmp_moneysum['money'], ':type' => 2])
        ->orderby(['id'=>SORT_DESC])
        ->one();

        return !empty($permsale) ? $permsale : NULL;
    }

    /**
     * находим или создаем рублевую скидку и привязываем ее к студенту
     * @param float $value
     * @param integer $sid
     * @return integer
     */
    public static function applyRubSale($value, $sid)
    {
        $rubsale = (new \yii\db\Query())
        ->select('s.id as id')
        ->from('calc_sale s')
        ->where('s.visible=:one AND s.procent=:zero AND value=:value', 
            [':one' => 1, ':zero' => 0, 'value' => $value])
        ->one();

        if (!empty($rubsale)) {
            $salestud = Salestud::find()->where('calc_studname=:student AND calc_sale=:sale', 
            [':student' => $sid, ':sale' => $rubsale['id']])->one();
            if ($salestud !== NULL) {
                $salestud->visible = 1;
                $salestud->save();
                return $salestud->id;
            } else {
                return static::addSaleToStudent($sid, $rubsale['id']);    
            }            
        } else {
            /* создаем новую рублевую скидку и привязываем ее к клиенту */
            $sale = Sale::createSale("Скидка корректирующая $value р.", 0, $value, 0);
            if ($sale > 0) {
                return static::addSaleToStudent($sid, $sale);
            } else {
                return 0;
            }
        }
    }

    /**
     * привязываем рублевую скидку к студенту
     * @param integer $sid
     * @return integer
     */
    public static function addSaleToStudent($student, $sale)
    {
        /* привязываем скидку к клиенту */
        $salestud = new Salestud();
        $salestud->calc_studname    = $student;
        $salestud->calc_sale        = $sale;
        $salestud->user             = Yii::$app->session->get('user.uid');
        $salestud->data             = date('Y-m-d');
        $salestud->visible          = 1;
        $salestud->data_visible     = '0000-00-00';
        $salestud->user_visible     = 0;
        $salestud->data_used        = '0000-00-00';
        $salestud->user_used        = 0;
        /* если скидка назначается руководителем, сразу подтверждаем */
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $salestud->approved     = 1;
        } else {
            $salestud->approved     = 0;
        }

        if ($salestud->save()) {
            return $salestud->id;
        } else {
            return 0;    
        }
    }

    /**
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
     * вызывается из StudnameContoller.php actionMerge
     * @param integer @id1
     * @param integer @id2
     * @return boolean
     */
    public static function changeStudentId($id1, $id2)
    {
        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update(self::tableName(), ['calc_studname' => $id1], ['calc_studname' => $id2])
        ->execute();

        return ($sql == 0) ? false : true;
    }
}
