<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use app\models\Message;
use app\models\UploadForm;
use app\models\User;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;


/**
 * MessageController implements the CRUD actions for CalcMessage model.
 */
class MessageController extends Controller
{
    public function behaviors()
    {
        return [
        'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view','create','update','delete','response','upload','send','ajaxgroup'],
                'rules' => [
                    [
                        'actions' => ['index','view','create','update','delete','response','upload','send','ajaxgroup'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','view','create','update','response','upload','send','ajaxgroup'],
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
                    'ajaxgroup' => ['post'],
                    'ajaxmess' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CalcMessage models.
     * @return mixed
     */
    public function actionIndex()
    {
        // подключаем боковое меню
        $this->layout = "column2";
        // по умолчанию выводим входящие сообщения
        $type = 'in';
        //задаем переменные для типа сообщения
        if(Yii::$app->request->get('type')){
            if(Yii::$app->request->get('type')=='out'){
                $type='out';
            }
        }
        // определяем текущие месяц и год
        $curmon = date('n');
        $curyear = date('Y');
        // если месяц передан в GET
        if(Yii::$app->request->get('mon')) {
            if(Yii::$app->request->get('mon')>=1&&Yii::$app->request->get('mon')<=12) {
                $mon = Yii::$app->request->get('mon');
            }
            // если нужны сообщения за все месяцы ставим NULL
            elseif(Yii::$app->request->get('mon')=='all') {
                $mon = NULL;
            } else {
                // по умолчанию месяц равен текущему
                $mon = $curmon;
            }
        } else{
            // по умолчанию месяц равен текущему
            $mon = $curmon;
        }
        // если год задан в GET
        if(Yii::$app->request->get('year')){
            if(Yii::$app->request->get('year')>=2012&&Yii::$app->request->get('year')<=$curyear){
                $year = Yii::$app->request->get('year');
            }
            // если нужны сообщения за все года ставим NULL
            elseif(Yii::$app->request->get('year')=='all') {
                $year = NULL;
            } else {
                // по умолчанию год равен текущему
                $year = $curyear;
            }
        } else {
            // по умолчанию год равен текущему
            $year = $curyear;
        }

        // выбираем id непрочитанных сообщений
        $notresponded = (new \yii\db\Query())
        ->select('mr.calc_message as mid, mr.id as rid')
        ->from('calc_messreport mr')
        ->leftJoin('calc_message m', 'mr.calc_message=m.id')
        ->where('mr.send=:send and m.send=:send and mr.user=:id and mr.ok=:zero and m.visible=:send',[':send'=>1, ':id'=>Yii::$app->session->get('user.uid'), ':zero'=>0])
        ->all();
        // проверяем что не пустой
        if(!empty($notresponded)){
            // задаем пустой массив
            $messid = [];
            // распечатываем и заполняем массив
            foreach($notresponded as $nrd){
                $messid[$nrd['rid']] = $nrd['mid'];
            }
            // уничтожаем ненужные переменные
            unset($notresponded);
            unset($nrd);
            // зануляем месяц и год, для выдачи всех непрочитанных сообщений
            $mon = NULL;
            $year = NULL;
        } else {
            // если непрочитанных сообщений нет то NULL
            $messid = NULL;
        }

        // выбираем сообщения пользователя
        $messages = (new \yii\db\Query()) 
        ->select('cm.id as mid, cm.name as mtitle, cm.description as mtext, cm.files as mfile, cm.user as msid, u1.name as musname, csn1.name as mstsname, cm.data as mdate, cm.send as msend, cm.calc_messwhomtype as mgroupid, cmwt.name as mgroupname, u2.name as murname, csn2.name as mstrname, cm.refinement_id as mrid')
        ->from('calc_message cm')
        ->leftJoin('user u1', 'u1.id=cm.user')
        ->leftJoin('calc_studname csn1', 'csn1.id=cm.user')
        ->leftJoin('user u2', 'u2.id=cm.refinement_id')
        ->leftJoin('calc_studname csn2', 'csn2.id=cm.refinement_id')
        ->leftJoin('calc_messwhomtype cmwt', 'cmwt.id=cm.calc_messwhomtype');
        if($type=='in'){
            $messages = $messages->leftJoin('calc_messreport cmr', 'cmr.calc_message=cm.id');
            $messages = $messages->where('cmr.user=:id and cm.visible=:vis',[':id'=>Yii::$app->session->get('user.uid'), ':vis'=>1]);
        } 
        if($type=='out'){
            if(\Yii::$app->session->get('user.ustatus')==3) {
                $messages = $messages->where('cm.visible=:vis',[':vis'=>1]);
            } else {
                $messages = $messages->where('cm.user=:id and cm.visible=:vis',[':id'=>Yii::$app->session->get('user.uid'), ':vis'=>1]);
            }
        }
        $messages = $messages->andFilterWhere(['month(cm.data)'=>$mon]);
        $messages = $messages->andFilterWhere(['year(cm.data)'=>$year]);
        $messages = $messages->andFilterWhere(['in', 'cm.id', $messid]);
        $messages = $messages->all();

        if($type=='out'){
            // выбираем колич получателей сообщений для отчетности
            $reprsp = (new \yii\db\Query())
            ->select('count(cmr.id) as num, cm.id as mid')
            ->from('calc_messreport cmr')
            ->leftJoin('calc_message cm','cm.id=cmr.calc_message')
            ->where('cmr.ok=:ok and cmr.send=:send and cm.send=:send',[':ok'=>1, ':send'=>1])
            ->andFilterWhere(['month(cm.data)'=>$mon])
            ->andFilterWhere(['year(cm.data)'=>$year])
            ->groupBy(['cm.id'])
            ->all();
            // выбираем колич прочтений для отчетности
            $repall = (new \yii\db\Query())
            ->select('count(cmr.id) as num, cm.id as mid')
            ->from('calc_messreport cmr')
            ->leftJoin('calc_message cm','cm.id=cmr.calc_message')
            ->where('cmr.send=:send and cm.send=:send',[':send'=>1])
            ->andFilterWhere(['month(cm.data)'=>$mon])
            ->andFilterWhere(['year(cm.data)'=>$year])
            ->groupBy(['cm.id'])
            ->all();
        } else {
            $reprsp = [];
            $repall = [];
        }
        return $this->render('index', [
            'messages'=>$messages,
            'reprsp'=>$reprsp,
            'repall'=>$repall,
            'messid'=>$messid,
        ]);
    }

    /**
     * Displays a single CalcMessage model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'message' => Message::getMessageById($id),
            'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }

    /**
     * Creates a new CalcMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // подключаем боковое меню
        $this->layout = "column2";

        $mtypes = [1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>12, 7=>13];

        // создаем пустой обьект
        $model = new Message();
        // если роль == преподаватель
        if(Yii::$app->session->get('user.ustatus')==5){
            $cmwts = [5 => Yii::t('app','To one user')];
        } else {
            //для остальных выбираем список всех типов сообщений
            $cmwts = (new \yii\db\Query())
            ->select('id as tid, name as tname')
            ->from('calc_messwhomtype')
            ->where('visible=:vis',[':vis'=>1])
            ->andWhere(['in','id',$mtypes])
            ->orderby(['id'=>SORT_ASC])
            ->all();
    }
        // если модель загружена и сохранена
        if ($model->load(Yii::$app->request->post())) {
            // добавляем служебные параметры в модель
            // тип сообщения по дефолту обычное, для чего был нужен longmess никто не знает
            $model->longmess = 0;
            // указываем автора сообщения
            $model->user = Yii::$app->session->get('user.uid');
            // указываем дату и время создания
            $model->data = date('Y-m-d H:i:s');
            // по дефолтку сообщение не отправлено
            $model->send = 0;
            // поле файлов по дефолту ноль, файл крепится после создания сообщения, но до отправки
            $model->files = '0';
            // помечаем сообщение как действующее
            $model->visible = 1;
            // сохраняем модель
            $model->save();
            // возвращаем в список исходящих сообщений пользователя
            return $this->redirect(['index','type'=>'out']);
        } else {
            return $this->render('create', [
                'model' => $model,
            'cmwts' => $cmwts,
            ]);
        }
    }

    /**
     * Updates an existing CalcMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        // подключаем боковое меню
    $this->layout = "column2";
        $model = $this->findModel($id);
    
        $mtypes = [1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>12, 7=>13];

        // если роль == преподаватель
        if(Yii::$app->session->get('user.ustatus')==5){
            $cmwts = [5 => Yii::t('app','To one user')];
        } else {
            // для остальных выбираем список всех типов сообщений
            $cmwts = (new \yii\db\Query())
            ->select('id as tid, name as tname')
            ->from('calc_messwhomtype')
            ->where('visible=:vis',[':vis'=>1])
            ->andWhere(['in','id',$mtypes])
            ->orderby(['id'=>SORT_ASC])
            ->all();
        }
        // если сообщение предназначено сотруднику
        if($model->calc_messwhomtype==5) {
            $table = 'user';
            $sender = Yii::$app->session->get('user.uid');
        }
    // если сообщение предназначено студенту
    elseif($model->calc_messwhomtype==13) {
            $table = 'calc_studname';
            $sender = NULL;
        } else {
            $table = 'user';
            $sender = NULL;
        }
        $reciever = [];
        
        $recievers = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from($table)
        ->where('visible=:vis', [':vis'=>1])
        ->andFilterWhere(['!=','id',$sender])
        ->all();

        foreach($recievers as $r){
            $reciever[$r['id']] = $r['name'];
        }
        unset($recievers);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // возвращаем в список исходящих сообщений пользователя
            return $this->redirect(['index','type'=>'out']);
        } else {
            return $this->render('update', [
                'model' => $model,
        'cmwts' => $cmwts,
                'reciever' => $reciever,
            ]);
        }
    }

    /**
     * Deletes an existing CalcMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CalcMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Метод ищет непрочитанное сообщение по report id и user id,
     * и помечает его прочитанным.
     * @param integer $id
     */
    public function actionResponse($rid = NULL)
    {
        if (Yii::$app->request->isPost) {
            /* включаем формат ответа JSON */
            Yii::$app->response->format = Response::FORMAT_JSON;
            $id = (int)Yii::$app->request->post('id');
            if ($id) {
                /* проверяем что сообщение не прочитано и получаем его id */
                $message = Message::getUnreadMessageIdByReportId($id, Yii::$app->session->get('user.uid'));

                /* если есть непрочитанное сообщение меняем его статус на прочитанное */
                if(!empty($message)){
                    $db = (new \yii\db\Query())
                    ->createCommand()
                    ->update('calc_messreport', ['ok' => '1'], ['id'=>$message['id']])
                    ->execute();

                    return [ 'result' => true ];
                } else {
                    return [
                        'result' => false,
                        'errMessage' => 'Сообщение №' . $id . ' не найдено.'
                    ];
                }
            } else {
                return [
                    'result' => false,
                    'errMessage' => 'Идентификатор сообщения не задан.'
                ];
            }
        } else {
            if ($rid) {
                /* проверяем что сообщение не прочитано и получаем его id */
                $message = Message::getUnreadMessageIdByReportId($rid, Yii::$app->session->get('user.uid'));
                /* если есть непрочитанное сообщение меняем его статус на прочитанное */
                if (!empty($message)) {
                    $db = (new \yii\db\Query())
                    ->createCommand()
                    ->update('calc_messreport', ['ok' => '1'], ['id' => $message['id']])
                    ->execute();
                    return $this->redirect(['message/index']);
                } else {
                    throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));    
                }
            } else {
                throw new BadRequestHttpException(Yii::t('yii', 'Missing required arguments: { rid }'));
            }
        }
    }
    
    public function actionUpload($id)
    {
        $this->layout = 'column2';
        if(Yii::$app->request->get('id')) {
            $message = $this->findModel($id);
            if(!empty($message)) {

                $file = (new \yii\db\Query())
                ->select('id as mid, name as mname, files as mfile, data as mdate')
                ->from('calc_message')
                ->where('id=:id and send!=:send', [':id'=>\Yii::$app->request->get('id'),':send'=>1])
                ->one();

                $model = new UploadForm();

                if(Yii::$app->request->isPost) {
                    $model->file = UploadedFile::getInstance($model, 'file');

                    if($model->file && $model->validate()) {
                //задаем адрес папки с файлами для поиска
                $spath = "uploads/calc_message/";
                //задаем адрес папки для загрузки файла
                $filepath = "uploads/calc_message/".$id."/fls/";
                //задаем имя файла
                $filename = "file-".$id.".".$model->file->extension;
                //проверяем наличие файла и папки
                $filesearch = FileHelper::findFiles($spath,['only'=>[$filename]]);
                if(empty($filesearch)){
                    FileHelper::createDirectory($spath.$id."/");
                FileHelper::createDirectory($spath.$id."/fls/");
                }
                $model->file->saveAs($filepath.$filename);
                $db = (new \yii\db\Query())
                ->createCommand()
                ->update('calc_message', ['files' => $filename,'data'=>$file['mdate']], ['id'=>$id])
                ->execute();
                return $this->redirect(['message/upload','id'=>$id]);
                }
                }
                return $this->render('upload', ['model' => $model,'file'=>$file]);
            } else {
                return $this->redirect(['message/index']);
        }
        } else {
        return $this->redirect(['message/index']);
        }
    }
    
    public function actionSend($id)
    {    
        //ищем сообщение
        $mess =  (new \yii\db\Query())
        ->select('cm.id as mid, cm.calc_messwhomtype as mgroup, cm.user as sender, cm.refinement_id as reciever, cm.data as mdate')
        ->from('calc_message cm')
        ->where('cm.send=:send and cm.id=:id',[':send'=>0,':id'=>$id])
        ->one();
        
        if(!empty($mess) && $mess['sender']==Yii::$app->session->get('user.uid')){
            //если сообщение только для одного юзера
            if($mess['mgroup']==5||$mess['mgroup']==13){
            //пишем строку в журнал отправки
                $report = (new \yii\db\Query())
                ->createCommand()
                ->insert('calc_messreport', ['calc_message'=>$mess['mid'],'user'=>$mess['reciever'],'ok'=>0,'data'=>date('Y-m-d H:i:s'),'send'=> 1])
                ->execute();
                unset($report);
            } else {
                $recievers = [];
                switch($mess['mgroup']) {
                    case 1:
                        $recievers = (new \yii\db\Query())
                        ->select('id as id')
                        ->from('user u')
                        ->where('visible=:vis', [':vis'=>1])
                        ->all();
                        break;
                    case 2:
                        $recievers = (new \yii\db\Query())
                        ->select('id as id')
                        ->from('user u')
                        ->where('status=:role and visible=:vis', [':role'=>3, ':vis'=>1])
                        ->all();
                        break;
                   case 3:
                        $recievers = (new \yii\db\Query())
                        ->select('id as id')
                        ->from('user u')
                        ->where('status=:role and visible=:vis', [':role'=>4, ':vis'=>1])
                        ->all();
                        break;
                   case 4:
                        $recievers = (new \yii\db\Query())
                        ->select('u.id as id')
                        ->from('user u')
                        ->join('INNER JOIN', 'calc_teacher t', 't.id=u.calc_teacher')
                        ->where('t.visible=:vis and u.visible=:vis', [':vis'=>1])
                        ->all();
                        break;
                }
                if(!empty($recievers)) {
                    foreach($recievers as $r) {
                        //пишем строку в журнал отправки
                        $report = (new \yii\db\Query())
                        ->createCommand()
                        ->insert('calc_messreport', ['calc_message'=>$mess['mid'],'user'=>$r['id'],'ok'=>0,'data'=>date('Y-m-d H:i:s'),'send'=> 1])
                        ->execute();
                    }
                }
                unset($report);
                unset($recievers);
                unset($r);
            }            
            //помечаем сообщение как отправленное
            $message = (new \yii\db\Query())
            ->createCommand()
            ->update('calc_message', ['send' => 1], ['id'=>$id])
            ->execute();
        }
    return $this->redirect(['message/index','type'=>'out']);
    }

    public function actionAjaxgroup() 
    {
        if (Yii::$app->request->isAjax){
            if(Yii::$app->request->post('type')!='5'&&Yii::$app->request->post('type')!='13'){
                $users = "<input type='hidden' id='message-refinement_id' class='form-control' name='Message[refinement_id]' value='0'><div class='help-block'></div>";
            } else {
                //если получатель сотрудник
                if(Yii::$app->request->post('type')=='5'){
                    $susers = (new \yii\db\Query())
                    ->select('u.id as uid, u.name as uname')
                    ->from('user u')
                    ->where('u.visible=:vis and u.id!=:uid', [':vis'=>1, ':uid'=>Yii::$app->session->get('user.uid')])
                    ->orderBy(['u.name'=>SORT_ASC])
                    ->all();
                }

                //если получатель студент
                elseif(Yii::$app->request->post('type')=='13'){
                    $susers = (new \yii\db\Query())
                    ->select('cs.id as uid, cs.name as uname')
                    ->from('calc_studname cs')
                    ->leftJoin('tbl_client_access tca','tca.calc_studname=cs.id')
                    ->where('cs.visible=:vis and active=:vis and tca.site=:vis', [':vis'=>1])
                    ->orderBy(['cs.name'=>SORT_ASC])
                    ->all();
                }

                $users = "<label class='control-label' for='message-refinement_id'>".\Yii::t('app','Reciever')."</label>"; 
                $users .= "<select id='message-refinement_id' class='form-control' name='Message[refinement_id]'>";
                $users .= "<option value=''>".Yii::t('app','-select-')."</option>";
                foreach($susers as $suser){
                   $users .= "<option value='".$suser['uid']."'>".$suser['uname']." (#".$suser['uid'].")</option>";
                }
                $users .= "</select>";
                $users .= "<div class='help-block'></div>";
            }
            return $users;
        }
    }

    public function actionDisable($id)
    {
    // получаем информацию по пользователю
    $model=$this->findModel($id);
    //проверяем текущее состояние
    if($model->visible==1){
        $model->visible = 0;
        $model->save();
    }
    return $this->redirect(['index']);
    }

}
