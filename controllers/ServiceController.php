<?php

namespace app\controllers;

use app\models\Service;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;

/**
 * ServiceController implements the CRUD actions for Service model.
 */
class ServiceController extends Controller
{
    public function behaviors()
    {
        $rules = ['index', 'create', 'update', 'delete'];
        return [
	        'access' => [
                'class' => AccessControl::class,
                'only' => $rules,
                'rules' => [
                    [
                        'actions' => $rules,
                        'allow'   => false,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => $rules,
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Service models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!in_array(Yii::$app->session->get('user.ustatus'), [3, 4])) {
            throw new ForbiddenHttpException('Access denied');
        }
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
        $tss_condition = ['like', 's.name', $url_params['TSS']];
        if ((int)$url_params['TSS'] > 0) {
            $tss_condition = ['s.id' => (int)$url_params['TSS']];
        }
        // пишем запрос
        $services = (new \yii\db\Query()) 
        ->select([
            'sid'       => 's.id',
            'sname'     => 's.name',
            'sdate'     => 's.data',
            'cid'       => 'c.id',
            'cname'     => 'c.name',
            'cstid'     => 'st.id',
            'cstname'   => 'st.name',
            'cstnid'    => 'sn.id',
            'cstnvalue' => 'sn.value',
            'ctnid'     => 'tn.id',
            'ctnname'   => 'tn.name'
        ])
        ->from(['s' => 'calc_service'])
        ->leftjoin(['c' => 'calc_city'], 'c.id = s.calc_city')
        ->leftjoin(['st' => 'calc_servicetype'], 'st.id = s.calc_servicetype')
        ->leftjoin(['sn' => 'calc_studnorm'], 'sn.id = s.calc_studnorm')
        ->leftjoin(['tn' => 'calc_timenorm'], 'tn.id = s.calc_timenorm')
        ->where(['s.visible' => 1])
        ->andWhere(['not', ['sn.id' => null]])
        ->andFilterWhere($tss_condition)
        ->andFilterWhere(['s.calc_servicetype' => $url_params['STID']])
        ->andFilterWhere(['s.calc_city' => $url_params['SCID']])
        ->andFilterWhere(['s.calc_lang' => $url_params['SLID']])
        ->andFilterWhere(['s.calc_eduage' => $url_params['SAID']])
        ->andFilterWhere(['s.calc_eduform' => $url_params['SFID']])
        ->andFilterWhere(['<', 's.data', $before])
        ->andFilterWhere(['>', 's.data', $after]);

        $countQuery = clone $services;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $services = $services->orderby(['c.id' => SORT_ASC, 's.data' => SORT_DESC, 's.id' => SORT_ASC]);
        if ($url_params['page']) {
            $offset = 25 * (Yii::$app->request->get('page') - 1);
            $services = $services->limit($limit)->offset($offset)->all();
        } else{
            $services = $services->limit($limit)->all();
        }

        return $this->render('index', [
                'cities'        => Service::getServiceDataForSelectSimple('calc_city'),
                'eduages'       => Service::getServiceDataForSelectSimple('calc_eduage'),
                'eduforms'      => Service::getServiceDataForSelectSimple('calc_eduform'),
                'languages'     => Service::getServiceDataForSelectSimple('calc_lang'),
                'pages'         => $pages,
                'services'      => $services,
                'servicetypes'  => Service::getServiceDataForSelectSimple('calc_servicetype'),
                'types'         => ['all' => 'Все услуги', 'actual' => 'Актуальные','unactual' => 'Устаревшие'],
                'url_params'    => $url_params,
                'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * Creates a new Service model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3) {
            throw new ForbiddenHttpException('Access denied');
        }
        $userInfoBlock = User::getUserInfoBlock();
        $model = new Service();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Услуга #{$model->id} успешно создана");
                return $this->redirect(['service/index', 'TSS' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', "Не удалось создать услугу");
            }
        }
        return $this->render('create', [
            'model' => $model,
            'servicetypes'  => Service::getServiceDataForSelectSimple('calc_servicetype'),
            'cities'        => Service::getServiceDataForSelectSimple('calc_city'),
            'languages'     => Service::getServiceDataForSelectSimple('calc_lang'),
            'eduages'       => Service::getServiceDataForSelectSimple('calc_eduage'),
            'eduforms'      => Service::getServiceDataForSelectSimple('calc_eduform'),
            'timenorms'     => Service::getServiceDataForSelectSimple('calc_timenorm'),
            'studnorms'     => Service::getServiceDataForSelectSimple('calc_studnorm'),
            'userInfoBlock' => $userInfoBlock
        ]);
    }

    /**
     * Updates an existing Service model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3) {
            throw new ForbiddenHttpException('Access denied');
        }
        $model = $this->findModel($id);        
        $current_state = Service::getServiceCurrentState($id);
        
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save(true, ['data', 'name', 'calc_city', 'calc_studnorm'])) {

                }
                if ($current_state['snid'] != $model->calc_studnorm) {
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
                $transaction->commit();
                Yii::$app->session->setFlash('success', "Услуга #{$model->id} успешно обновлена");

                return $this->redirect(['service/index', 'TSS' => $model->id]);
            } catch (\Exception $e) {
                $transaction->rollback();
                Yii::$app->session->setFlash('error', "Не удалось обновить услугу #{$model->id}");
            }
        }
        
        return $this->render('update', [
            'model'          => $model,
            'studnorms'      => Service::getServiceDataForSelectSimple('calc_studnorm'),
            'servicechanges' => Service::getServiceHistory($id),
            'cities'         => Service::getServiceDataForSelectSimple('calc_city'),
            'userInfoBlock'  => User::getUserInfoBlock()
        ]);
    }

    public function actionDelete($id)
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3) {
            throw new ForbiddenHttpException('Access denied');
        }
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', "Услуга #{$model->id} успешно удалена");
        } else {
            Yii::$app->session->setFlash('error', "Не удалось удалить услугу #{$model->id}");
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Service model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Service the loaded model
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
