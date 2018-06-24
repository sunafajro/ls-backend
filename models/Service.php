<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_service".
 *
 * @property integer $id
 * @property integer $visible
 * @property integer $calc_eduage
 * @property integer $calc_lang
 * @property integer $calc_eduform
 * @property string $name
 * @property integer $calc_studnorm
 * @property string $data
 * @property integer $calc_timenorm
 * @property integer $calc_city
 * @property integer $calc_servicetype
 */
class Service extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible', 'calc_eduage', 'calc_lang', 'calc_eduform', 'name', 'calc_studnorm', 'data', 'calc_timenorm', 'calc_city', 'calc_servicetype'], 'required'],
            [['visible', 'calc_eduage', 'calc_lang', 'calc_eduform', 'calc_studnorm', 'calc_timenorm', 'calc_city', 'calc_servicetype'], 'integer'],
            [['name'], 'string'],
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
            'visible' => 'Visible',
            'calc_eduage' => 'Возраст клиентов',
            'calc_lang' => 'Язык',
            'calc_eduform' => 'Форма обучения',
            'name' => 'Название',
            'calc_studnorm' => 'Норма оплаты',
            'data' => 'Действительна до',
            'calc_timenorm' => 'Норма времени',
            'calc_city' => 'Город',
            'calc_servicetype' => 'Тип услуги',
        ];
    }
    
    /* получаем доступные данные для селектов (многомерный массив) */
    public static function getServiceDataForSelect($table)
    { 
        $data = (new \yii\db\Query())
        ->select('id, name')
        ->from($table)
        ->where('visible=:visible', [':visible' => 1])
        ->orderby(['name'=>SORT_ASC])
        ->all();
        
        return $data;
    }
    /* получаем доступные данные для селектов */
    
    /* получаем доступные данные для селектов в виде одномерного массива */
    public static function getServiceDataForSelectSimple($table)
    {
        $array_data = [];
        $data = [];
        
        $array_data = self::getServiceDataForSelect($table);
        
        foreach($array_data as $ad) {
            $data[$ad['id']] = $ad['name'];
        }
        
		return array_unique($data);
    }
    /* получаем список городов для услуг в виде одномерного массива */
    
    /* получаем получаем массив с историей изменений услуги */
    public static function getServiceHistory($id)
    {
        $servicechanges = (new \yii\db\Query())
        ->select('csh.date as date, csh.value as value, u.name as user')
        ->from('calc_servicehistory csh')
        ->leftjoin('user u', 'u.id=csh.user')
        ->where('csh.calc_service=:id', [':id'=>$id])
        ->orderby(['csh.id'=>SORT_ASC])
        ->all();
        
        return $servicechanges;
    }
    /* получаем получаем массив с историей изменений услуги */

    /* получаем получаем массив с текущими параметрами услуги */
    public static function getServiceCurrentState($id)
    {
        $current_state = (new \yii\db\Query())
        ->select('cs.id as id, cs.calc_studnorm as snid, csn.value as value')
        ->from('calc_service cs')
        ->leftjoin('calc_studnorm csn', 'csn.id=cs.calc_studnorm')
        ->where('cs.id=:id', [':id'=>$id])
        ->one();
    
        return $current_state;
    }
    /* получаем получаем массив с текущими параметрами услуги */

    public static function getServiceValue($sid)
    {
        $service = (new \yii\db\Query())
        ->select('sn.value as value')
        ->from('calc_service s')
        ->leftjoin('calc_studnorm sn', 'sn.id=s.calc_studnorm')
        ->where('s.id=:id', [':id' => $sid])
        ->one();
    
        return !empty($service) ? $service['value'] : NULL;
    }

    /* возвращает массив услуг для выпадающего списка в форме создания счета */
    public static function getInvoiceServicesList()
    {
        if((int)Yii::$app->session->get('user.ustatus') === 4) {
            $city = Yii::$app->session->get('user.ucity');
        } else {
            $city = NULL;
        }

        $serv = NULL;
        if($city) {
            // задаем массив город для услуг первый тип общешкольные
            $serv[0] = 3;
            $serv[1] = (int)$city; 
        }
        $services = (new \yii\db\Query())
        ->select('s.id as id, s.name as name, sn.value as value')
        ->from('calc_service s')
        ->leftJoin('calc_studnorm sn', 'sn.id=s.calc_studnorm')
        ->where('s.visible=:one and sn.visible=:one and s.data>:data',
        [':one' => 1, ':data'=>date('Y-m-d')])
        ->andWhere(['in', 's.calc_servicetype', [1, 3]])
        ->andFilterWhere(['in', 'calc_city', $serv])
        ->orderby(['s.id'=>SORT_ASC])
        ->all();

        return $services;
    }

    /* возвращает массив услуг для выпадающего списка в форме создания счета */
    public static function getInvoiceServicesListSimple()
    {
        $services = [];

        $tmp_services = static::getInvoiceServicesList();

        foreach($tmp_services as $s){
            $services[$s['id']] = "# ".$s['id']." ".$s['name'];
        }

        return !empty($services) ? $services : NULL;
    }
}
