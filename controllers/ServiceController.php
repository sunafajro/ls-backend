<?php

namespace app\controllers;

use Yii;
use app\models\Service;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;
/**
 * ServiceController implements the CRUD actions for CalcService model.
 */
class ServiceController extends Controller
{
    public function behaviors()
    {
        return [
	    'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','create','update','delete'],
                'rules' => [
                    [
                        'actions' => ['index','create','update','delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','create','update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                     [
                        'actions' => ['delete'],
                        'allow' => false,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CalcService models.
     * @return mixed
     */
    public function actionIndex()
    {
        $userInfoBlock = User::getUserInfoBlock();
        if(Yii::$app->session->get('user.ustatus') == 3 || Yii::$app->session->get('user.ustatus') == 4) {
            $url_params = self::getUrlParams();
            $limit = 25;
            $offset = 0;
            
            $before = NULL;
            $after = NULL;

            switch ($url_params['type']) {
                case 'unactual': $before = date('Y-m-d'); break;
                case 'actual': $after = date('Y-m-d'); break;
            }

            // по умолчанию поиск по имени
            // если в строке целое число, то поиск по идентификатору
            $tss_condition = ['like', 'cs.name', $url_params['TSS']];
            if ((int)$url_params['TSS'] > 0) {
              $tss_condition = ['cs.id' => (int)$url_params['TSS']];
            }
            // пишем запрос
            $services = (new \yii\db\Query()) 
            ->select([
                'sid' => 'cs.id',
                'sname' => 'cs.name',
                'sdate' => 'cs.data',
                'cid' => 'cs.calc_city',
                'cname' => 'cc.name',
                'cstid' => 'cst.id',
                'cstname' => 'cst.name',
                'cstnid' => 'cstn.id',
                'cstnvalue' => 'cstn.value',
                'ctnid' => 'ctn.id',
                'ctnname' => 'ctn.name'
            ])
            ->from(['cs' => 'calc_service'])
            ->leftjoin('calc_city cc', 'cc.id=cs.calc_city')
            ->leftjoin('calc_servicetype cst', 'cst.id=cs.calc_servicetype')
            ->leftjoin('calc_studnorm cstn', 'cstn.id=cs.calc_studnorm')
            ->leftjoin('calc_timenorm ctn', 'ctn.id=cs.calc_timenorm')
            ->where(['cs.visible' => 1])
            ->andWhere(['not', ['cstn.id' => null]])
            ->andFilterWhere($tss_condition)
            ->andFilterWhere(['cs.calc_servicetype' => $url_params['STID']])
            ->andFilterWhere(['cs.calc_city' => $url_params['SCID']])
            ->andFilterWhere(['cs.calc_lang' => $url_params['SLID']])
            ->andFilterWhere(['cs.calc_eduage' => $url_params['SAID']])
            ->andFilterWhere(['cs.calc_eduform' => $url_params['SFID']])
            ->andFilterWhere(['<', 'cs.data', $before])
            ->andFilterWhere(['>', 'cs.data', $after]);
            // делаем клон запроса
            $countQuery = clone $services;
            // получаем данные для паджинации
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            // добавляем условия сортировки
            $services = $services->orderby(['cc.id'=>SORT_ASC, 'cs.data'=>SORT_DESC, 'cs.id'=>SORT_ASC]);
            // отрабатываем запрос с с ограничениями на колич строк
            if($url_params['page']){
                $offset = 25 * (Yii::$app->request->get('page') - 1);
                $services = $services->limit($limit)->offset($offset)->all();
            }
            // по дефолту выводим 25 строк начиная с первой
            else{
                $services = $services->limit($limit)->all();
            }

            // отправляем все во вьюз
            return $this->render('index', [
                  'services' => $services,
                  'pages' => $pages,
                  'servicetypes' => Service::getServiceDataForSelectSimple('calc_servicetype'),
                  'cities' => Service::getServiceDataForSelectSimple('calc_city'),
                  'languages' => Service::getServiceDataForSelectSimple('calc_lang'),
                  'eduages' => Service::getServiceDataForSelectSimple('calc_eduage'),
                  'eduforms' => Service::getServiceDataForSelectSimple('calc_eduform'),
                  'types' => ['all' => 'Все услуги', 'actual' => 'Актуальные','unactual' => 'Устаревшие'],
                  'url_params' => $url_params,
                  'userInfoBlock' => $userInfoBlock
            ]);
        } else {
            return $this->goBack();
        }
    }

    /**
     * Creates a new CalcService model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->session->get('user.ustatus') == 3) {
            $userInfoBlock = User::getUserInfoBlock();
            $model = new Service();

            if ($model->load(Yii::$app->request->post())) {
                $model->visible = 1;
                $model->save();
                return $this->redirect(['service/index']);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'servicetypes' => Service::getServiceDataForSelectSimple('calc_servicetype'),
                    'cities' => Service::getServiceDataForSelectSimple('calc_city'),
                    'languages' => Service::getServiceDataForSelectSimple('calc_lang'),
                    'eduages' => Service::getServiceDataForSelectSimple('calc_eduage'),
                    'eduforms' => Service::getServiceDataForSelectSimple('calc_eduform'),
                    'timenorms' => Service::getServiceDataForSelectSimple('calc_timenorm'),
                    'studnorms' => Service::getServiceDataForSelectSimple('calc_studnorm'),
                    'userInfoBlock' => $userInfoBlock
                ]);
            }
        } else {
            return $this->goBack();
        }
    }

    /**
     * Updates an existing CalcService model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->session->get('user.ustatus') == 3) {
            $userInfoBlock = User::getUserInfoBlock();
            $model = $this->findModel($id);        
            $current_state = Service::getServiceCurrentState($id);
            
            // если данные пришли и успешно обновились в базе
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                // сверяем не было ли изменений в норме оплаты
                if($current_state['snid'] != $model->calc_studnorm){
                  // если изменения были, пишем в таблицу истории
                  $db = (new \yii\db\Query())
                    ->createCommand()
                    ->insert('calc_servicehistory', 
                    [
                        'calc_service' => $current_state['id'], 
                        'calc_studnorm' => $current_state['snid'], 
                        'value' => $current_state['value'], 
                        'user' => Yii::$app->session->get('user.uid'),
                        'date' => date("Y-m-d H:m:s")
                    ])->execute();
                }
                /* возвращаемся в список услуг */
                return $this->redirect(['service/index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'studnorms' => Service::getServiceDataForSelectSimple('calc_studnorm'),
                    'servicechanges' => Service::getServiceHistory($id),
                    'cities' => Service::getServiceDataForSelectSimple('calc_city'),
                    'userInfoBlock' => $userInfoBlock
                ]);
            }
        } else {
            return $this->goBack();
        }
    }

    /**
     * Finds the CalcService model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcService the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Service::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected static function getUrlParams()
    {
        $url_params = [
            'service/index',
            'TSS' => NULL,
            'STID' => NULL,
            'SCID' => NULL,
            'SLID' => NULL,
            'SAID' => NULL,
            'SFID' => NULL,
            'type' => 'actual',
            'page' => NULL            
        ];

        if(Yii::$app->request->get('TSS') && Yii::$app->request->get('TSS') !== '') {
            $url_params['TSS'] = Yii::$app->request->get('TSS');
        }
        if(Yii::$app->request->get('STID') && Yii::$app->request->get('STID') !== 'all') {
            $url_params['STID'] = Yii::$app->request->get('STID');
        }
        if(Yii::$app->request->get('SCID') && Yii::$app->request->get('SCID') !== 'all') {
            $url_params['SCID'] = Yii::$app->request->get('SCID');
        }
        if(Yii::$app->request->get('SLID') && Yii::$app->request->get('SLID') !== 'all') {
            $url_params['SLID'] = Yii::$app->request->get('SLID');
        }
        if(Yii::$app->request->get('SAID') && Yii::$app->request->get('SAID') !== 'all') {
            $url_params['SAID'] = Yii::$app->request->get('SAID');
        }
        if(Yii::$app->request->get('SFID') && Yii::$app->request->get('SFID') !== 'all') {
            $url_params['SFID'] = Yii::$app->request->get('SFID');
        }
        if(Yii::$app->request->get('type')) {
            $url_params['type'] = Yii::$app->request->get('type');
        }

        if(Yii::$app->request->get('page') && Yii::$app->request->get('page') > 0){
            $url_params['page'] = Yii::$app->request->get('page');
        }
        return $url_params;
    }
}
