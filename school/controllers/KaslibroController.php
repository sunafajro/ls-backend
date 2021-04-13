<?php

namespace school\controllers;

use Yii;
use school\models\Kaslibro;
//use school\models\Kaslibro_client;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * @deprecated
 * KaslibroController implements the CRUD actions for Kaslibro model.
 */
class KaslibroController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update', 'delete', 'check', 'uncheck', 'done', 'undone'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'check', 'uncheck', 'done', 'undone'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'check', 'uncheck', 'done', 'undone'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Kaslibro models.
     * @return mixed
     */
    public function actionIndex()
    {
        // всех кроме руководителей и бухгалтера
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=4 && \Yii::$app->session->get('user.ustatus')!=8) {
            // редиректим обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
        // всех кроме руководителей и бухгалтера
        
        // определяем месяц для выборки
        if((int)\Yii::$app->request->get('month') > 0 && (int)\Yii::$app->request->get('month') < 13) {
            $month = (int)\Yii::$app->request->get('month');
        } else {
            $month = date('m');
        }
        // определяем месяц для выборки

        // определяем год для выборки
        if(\Yii::$app->request->get('year')) {
            $year = (int)\Yii::$app->request->get('year');
        } else {
            $year = date('Y');
        }
        // определяем год для выборки
        
        // выбираем сумму на начало месяца
        //$sum = (new \yii\db\Query())
        //->select('av_sum as av_sum, b_sum as b_sum, n_sum as n_sum')
        //->from('calc_kaslibro_sum')
        //->where('month=:m and year=:y and deleted=:d', [':m' => $month, ':y' => $year, ':d' => 0])
        //->one();
        // выбираем сумму на начало месяца

        $office = NULL;
        // для менеджеров определяем код офиса
        if(\Yii::$app->session->get('user.ustatus')==4) {
            $moffice = (new \yii\db\Query())
            ->select('id as id')
            ->from('calc_kaslibro_office')
            ->where('calc_office=:id', [':id'=>\Yii::$app->session->get('user.uoffice_id')])
            ->one();
            
            if(!empty($moffice)) {
                $office = $moffice['id'];    
            } else {
                $office = 0;
            }
            $offices = NULL;
        } else {
            if(\Yii::$app->request->get('office') && \Yii::$app->request->get('office') != 'all') {
                $office = \Yii::$app->request->get('office');
            }
            $offices = (new \yii\db\Query())
            ->select('id as id, name as name')
            ->from('calc_kaslibro_office')
            ->where('deleted=:zero', [':zero' => 0])
            ->all(); 
        }        
        // для менеджеров определяем код офиса
        
        // выбираем данные из таблицы
        $model = (new \yii\db\Query())
        ->select('kl.id as id, kl.date as date, klo.name as operation, kl.operation_detail as detail, klc.name as client, kle.name as executor, kl.month as month, klf.name as office, kld.name as code, kl.n_plus as n_plus, kl.n_minus as n_minus, kl.reviewed as reviewed, kl.done as done')
        ->from('calc_kaslibro kl')
        ->leftJoin('calc_kaslibro_operation klo', 'klo.id=kl.operation')
        ->leftJoin('calc_kaslibro_client klc', 'klc.id=kl.client')
        ->leftJoin('calc_kaslibro_executor kle', 'kle.id=kl.executor')
        ->leftJoin('calc_kaslibro_office klf', 'klf.id=kl.office')
        ->leftJoin('calc_kaslibro_code kld', 'kld.id=kl.code')
        ->where('kl.deleted=:d AND !(kl.n_plus=:d AND kl.n_minus=:d)', [':d' => 0])
        ->andFilterWhere(['kl.month' => $month])
        ->andFilterWhere(['kl.year' => $year])
        ->andFilterWhere(['kl.office' => $office])
        ->orderby(['kl.date' =>SORT_ASC, 'kl.id'=>SORT_ASC])
        //->limit(30)
        ->all();
        // выбираем данные из таблицы

        // выбираем список месяцев
        $arr_months = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_month')
        ->where('visible=:vis', [':vis' => 1])
        ->all();
        
        $months = [];
        foreach($arr_months as $m){
            $months[$m['id']] = $m['name'];
        }
        unset($m);
        unset($arr_months);
        // выбираем список месяцев
        
        return $this->render('index', [
            'model' => $model,
            'months' => $months,
            //'sum' => $sum,
            'month' => $month,
            'office' => $office,
            'offices' => $offices,
        ]);
    }


    /**
     * Creates a new Kaslibro model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // всех кроме руководителей, менеджеров и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=4 && \Yii::$app->session->get('user.ustatus')!=8) {
            // 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей, менеджеров и бухгалтера редиректим обратно
        
        $moffice = NULL;
        // для менеджеров определяем код офиса
        if(\Yii::$app->session->get('user.ustatus')==4) {
            $moffice = (new \yii\db\Query())
            ->select('id as id')
            ->from('calc_kaslibro_office')
            ->where('calc_office=:id', [':id'=>\Yii::$app->session->get('user.uoffice_id')])
            ->one();
        }
        // для менеджеров определяем код офиса
        
        $model = new Kaslibro();

        // Формируем массив с операциями
        $arr_operation = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_operation')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $operations = [];
        if($arr_operation && !empty($arr_operation)) {
            foreach($arr_operation as $o) {
                $operations[$o['id']] = $o['name'];
            }
            if(!empty($operations)) {
                natcasesort($operations);
            }
            unset($o);
            unset($arr_operation);
        }
        // Формируем массив с операциями

        // Формируем массив с клиентами
        $arr_client = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_client')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $clients = [];
        if($arr_client && !empty($arr_client)) {
            foreach($arr_client as $o) {
                $clients[$o['id']] = $o['name'];
            }
            if(!empty($clients)) {
                natcasesort($clients);
            }
            unset($o);
            unset($arr_client);
        }
        // Формируем массив с клиентами

        // Формируем массив с исполнителями
        $arr_executor = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_executor')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $executors = [];
        if($arr_executor && !empty($arr_executor)) {
            foreach($arr_executor as $o) {
                $executors[$o['id']] = $o['name'];
            }
            if(!empty($executors)) {
                natcasesort($executors);
            }
            unset($o);
            unset($arr_executor);
        }
        // Формируем массив с исполнителями
        
        // Формируем массив с офисами
        $arr_office = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_office')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $offices = [];
        if($arr_office && !empty($arr_office)) {
            foreach($arr_office as $o) {
                $offices[$o['id']] = $o['name'];
            }
            if(!empty($offices)) {
                natcasesort($offices);
            }
            unset($o);
            unset($arr_office);
        }
        // Формируем массив с офисами

        // Формируем массив с кодами
        $arr_code = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_code')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $codes = [];
        if($arr_code && !empty($arr_code)) {
            foreach($arr_code as $o) {
                $codes[$o['id']] = $o['name'];
            }
            if(!empty($offices)) {
                natcasesort($codes);
            }
            unset($o);
            unset($arr_code);
        }
        // Формируем массив с кодами

        // выбираем список месяцев
        $arr_months = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_month')
        ->where('visible=:vis', [':vis' => 1])
        ->all();
        
        $months = [];
        foreach($arr_months as $m){
            $months[$m['id']] = $m['name'];
        }
        unset($m);
        unset($arr_months);
        // выбираем список месяцев

        $years = [];
        $d = date('Y');
        while($d >= 2015) {
            $years[$d] = $d;
            $d--;
        }
        unset($d);
        
        if ($model->load(\Yii::$app->request->post())) {
            //$error = 0;
            $model->date = date('Y-m-d');
            $model->user = \Yii::$app->session->get('user.uid');
            
            switch(\Yii::$app->session->get('user.ustatus')) {
                case 3:
                    $model->reviewed = 1;
                    $model->done = 1;
                    break;
                case 4:
                    if(!empty($moffice) && $moffice!=NULL) {
                        $model->office =  $moffice['id'];
                    }
                    $model->reviewed = 0;
                    $model->done = 0;
                default:
                    $model->reviewed = 0;
                    $model->done = 0;
                    break;
            }
            $model->deleted = 0;
            
            /*
            //подменяем название клиента на id если термин уж есть, или создаем новый термин
            $model->client = trim($model->client);
            if($model->client != '') {
                $client_data = Kaslibro_client::find()->where('name=:name', [':name' => $model->client])->one();
                if(!empty($client_data)) {
                    $model->client = $client_data->name;
                } else {
                    $client = new Kaslibro_client();
                    $client->name = $model->client;
                    $client->deleted = 0;
                    $client->date = date('Y-m-d');
                    $client->user = \Yii::$app->session->get('user.uid');
                    if($client->save()) {
                        $model->client = $client->id;
                    } else {
                        $error = 1;
                    }
                }
            }
            //подменяем название клиента на id если термин уж есть, или создаем новый термин
            */

            if($model->save()) {
                if($model->reviewed == 0) {
                    $str = 'Запись успешно добавлена и ожидает проверки.';
                } else {
                    $str = 'Запись успешно добавлена.';
                }
                \Yii::$app->session->setFlash('success', $str);            
            } else {
                \Yii::$app->session->setFlash('error', 'Запись добавить не удалось.');
            }
            
            return $this->redirect(['index', 'month' => $model->month]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'operations' => $operations,
                'clients' => $clients,
                'executors' => $executors,
                'offices' => $offices,
                'months' => $months,
                'codes' => $codes,
                'moffice' => $moffice,
                'years' => $years,
            ]);
        }
    }

    /**
     * Updates an existing Kaslibro model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        // всех кроме руководителей, менеджеров и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=4 && \Yii::$app->session->get('user.ustatus')!=8) {
            // 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей, менеджеров и бухгалтера редиректим обратно
        
        $moffice = NULL;
        // для менеджеров определяем код офиса
        if(\Yii::$app->session->get('user.ustatus')==4) {
            $moffice = (new \yii\db\Query())
            ->select('id as id')
            ->from('calc_kaslibro_office')
            ->where('calc_office=:id', [':id'=>\Yii::$app->session->get('user.uoffice_id')])
            ->one();
        }
        // для менеджеров определяем код офиса
        
        $model = $this->findModel($id);
        
        // Формируем массив с операциями
        $arr_operation = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_operation')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $operations = [];
        if($arr_operation && !empty($arr_operation)) {
            foreach($arr_operation as $o) {
                $operations[$o['id']] = $o['name'];
            }
            if(!empty($operations)) {
                natcasesort($operations);
            }
            unset($o);
            unset($arr_operation);
        }
        // Формируем массив с операциями

        // Формируем массив с клиентами
        $arr_client = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_client')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $clients = [];
        if($arr_client && !empty($arr_client)) {
            foreach($arr_client as $o) {
                $clients[$o['id']] = $o['name'];
            }
            if(!empty($clients)) {
                natcasesort($clients);
            }
            unset($o);
            unset($arr_client);
        }
        // Формируем массив с клиентами

        // Формируем массив с исполнителями
        $arr_executor = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_executor')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $executors = [];
        if($arr_executor && !empty($arr_executor)) {
            foreach($arr_executor as $o) {
                $executors[$o['id']] = $o['name'];
            }
            if(!empty($executors)) {
                natcasesort($executors);
            }
            unset($o);
            unset($arr_executor);
        }
        // Формируем массив с исполнителями
        
        // Формируем массив с офисами
        $arr_office = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_office')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $offices = [];
        if($arr_office && !empty($arr_office)) {
            foreach($arr_office as $o) {
                $offices[$o['id']] = $o['name'];
            }
            if(!empty($offices)) {
                natcasesort($offices);
            }
            unset($o);
            unset($arr_office);
        }
        // Формируем массив с офисами

        // Формируем массив с кодами
        $arr_code = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_code')
        ->where('deleted=:del', [':del'=>0])
        ->all();
        
        $codes = [];
        if($arr_code && !empty($arr_code)) {
            foreach($arr_code as $o) {
                $codes[$o['id']] = $o['name'];
            }
            if(!empty($offices)) {
                natcasesort($codes);
            }
            unset($o);
            unset($arr_code);
        }
        // Формируем массив с кодами

        // выбираем список месяцев
        $arr_months = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_month')
        ->where('visible=:vis', [':vis' => 1])
        ->all();
        
        $months = [];
        foreach($arr_months as $m){
            $months[$m['id']] = $m['name'];
        }
        unset($m);
        unset($arr_months);
        // выбираем список месяцев

        $years = [];
        $d = date('Y');
        while($d >= 2015) {
            $years[$d] = $d;
            $d--;
        }
        unset($d);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'month' => $model->month]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'operations' => $operations,
                'clients' => $clients,
                'executors' => $executors,
                'offices' => $offices,
                'months' => $months,
                'codes' => $codes,
                'moffice' => $moffice,
                'years' => $years,
            ]);
        }
    }

    /**
     * Deletes an existing Kaslibro model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // всех кроме руководителей и бухгалтера
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=4 && \Yii::$app->session->get('user.ustatus')!=8) {
            // редиректим обратно
            return $this->redirect(Yii::$app->request->referrer);
        }
        
        $model = $this->findModel($id);
        
        if(!$model->deleted) {
            $model->deleted = 1;
            if($model->save()) {
                // задаем сообщеие об ошибке
                Yii::$app->session->setFlash('success', 'Запись успешно удалена!');
            } else {
                // задаем сообщеие об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось удалить запись!');
            }
        }

        return $this->redirect(['index', 'month' => $model->month]);
    }
    
    public function actionCheck($id)
    {
        // всех кроме руководителей и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=8) { 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей и бухгалтера редиректим обратно
        
        $model = $this->findModel($id);
        if(!$model->reviewed) {
            $model->reviewed = 1;
            if($model->save()) {
                // задаем сообщеие об ошибке
                \Yii::$app->session->setFlash('success', 'Запись успешно проверена!');
            } else {
                // задаем сообщеие об ошибке
                \Yii::$app->session->setFlash('error', 'Не удалось проверить запись!');
            }
        }

        return $this->redirect(['index', 'month' => $model->month]);
        
    }

    public function actionUncheck($id)
    {
        // всех кроме руководителей и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=8 && \Yii::$app->session->get('user.ustatus')!=4) { 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей и бухгалтера редиректим обратно
        
        $model = $this->findModel($id);
        if($model->reviewed) {
            $model->reviewed = 0;
            if($model->save()) {
                // задаем сообщеие об ошибке
                \Yii::$app->session->setFlash('success', 'Запись успешно переведена в непроверенные!');
            } else {
                // задаем сообщеие об ошибке
                \Yii::$app->session->setFlash('error', 'Не удалось перевести запись в непроверенные!');
            }
        }

        return $this->redirect(['index', 'month' => $model->month]);
        
    }

    public function actionDone($id)
    {
        // всех кроме руководителей и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=4 && \Yii::$app->session->get('user.ustatus')!=8) { 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей и бухгалтера редиректим обратно
        
        $model = $this->findModel($id);
        if(!$model->done) {
            $model->done = 1;
            if($model->save()) {
                // задаем сообщеие об ошибке
                \Yii::$app->session->setFlash('success', 'Запись успешно выполнена!');
            } else {
                // задаем сообщеие об ошибке
                \Yii::$app->session->setFlash('error', 'Не удалось выполнить запись!');
            }
        }

        return $this->redirect(['index', 'month' => $model->month]);
        
    }

    public function actionUndone($id)
    {
        // всех кроме руководителей и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=4 && \Yii::$app->session->get('user.ustatus')!=8) { 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей и бухгалтера редиректим обратно
        
        $model = $this->findModel($id);
        if($model->done) {
            $model->done = 0;
            if($model->save()) {
                // задаем сообщеие об ошибке
                \Yii::$app->session->setFlash('success', 'Запись успешно переведена в ожидающие выполнения!');
            } else {
                // задаем сообщеие об ошибке
                \Yii::$app->session->setFlash('error', 'Не удалось перевести запись в ожидающие выполнения!');
            }
        }

        return $this->redirect(['index', 'month' => $model->month]);
        
    }
    
    /*
	public function actionClientauto()
    {    
        $term = Yii::$app->request->get('term');
        //if($term=='') {
        //    $term = '';
        //}
        
        $data = (new \yii\db\Query())
        ->select(['name as name'])
		->from('calc_kaslibro_client')
        ->where(['like', 'name', $term])
		->limit(5)
        ->all();

		$i = 0;
		$list = [];
		if(!empty($data)) {
		    foreach($data as $d){
			    $list[$i]['value'] = $d['name'];
				$list[$i]['label'] = $d['name'];
				$i++;
		    }
			unset($d);
			unset($data);
		}
        echo json_encode($list);
        
    }
    */
    /**
     * Finds the Kaslibro model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Kaslibro the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Kaslibro::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
