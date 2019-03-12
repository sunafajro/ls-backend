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
use yii\web\ForbiddenHttpException;
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

    public function actionIndex()
    {
        $month = date('n');
        $month = $month < 10 ? '0' . $month : $month;
        if (Yii::$app->request->get('mon')) {
            if (Yii::$app->request->get('mon') >= 1 && Yii::$app->request->get('mon') <= 12) {
                $month = Yii::$app->request->get('mon');
            } elseif (Yii::$app->request->get('mon') === 'all') {
                $month = NULL;
            }
        }

        $year = date('Y');
        if (Yii::$app->request->get('year')) {
            if (Yii::$app->request->get('year') >= 2012 && Yii::$app->request->get('year') <= $year) {
                $year = Yii::$app->request->get('year');
            } elseif (Yii::$app->request->get('year')=='all') {
                $year = NULL;
            }
        }

        $unreaded = Message::getUnreadedMessagesIds();

        $start = NULL;
        $end = NULL;
        if ($month && $year) {
            $start = $year . '-' . $month . '-01';
            $end = date('Y-m-t', strtotime($start));
        }

        $incoming = Message::getUserMessages($start, $end, 'in');
        $outcoming = Message::getUserMessages($start, $end, 'out');

        $outcoming_ids = !empty($outcoming) ? [] : NULL;
        foreach ($outcoming as &$out) {
            $outcoming_ids[] = $out['id'];
            $out['direction'] = 'out';
        }
        unset($out);
        $messages = array_merge($incoming, $outcoming);

        $messages_readed = !empty($outcoming_ids) ? Message::getMessagesReadStatus($outcoming_ids, 1) : [];
        $messages_all = !empty($outcoming_ids) ? Message::getMessagesReadStatus($outcoming_ids, NULL) : [];

        return $this->render('index', [
            'messages' => $messages,
            'messages_readed' => $messages_readed,
            'messages_all' => $messages_all,
            'unreaded' => $unreaded,
            'month' => $month,
            'year' => $year,
            'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }

    /**
     * Displays a single CalcMessage model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $message = Message::getMessageById($id);
        if (!$message) {
            throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));
        }
        if (
            (int)$message['sender_id'] === (int)Yii::$app->session->get('user.uid')
            || (int)Yii::$app->session->get('user.ustatus') === 3
        ) {
            return $this->render('view', [
                'message' => $message,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }

    public function actionCreate()
    {
        $permitted_types = [1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>12, 7=>13];
        $types = Message::getMessageDirectionsArray($permitted_types);
        $model = new Message();
        if ($model->load(Yii::$app->request->post())) {
            $model->longmess = 0;
            $model->user = Yii::$app->session->get('user.uid');
            $model->data = date('Y-m-d H:i:s');
            $model->send = 0;
            $model->files = '0';
            $model->visible = 1;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Message successfully created!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to create message!'));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'types' => $types,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (
            (int)$model->user === (int)Yii::$app->session->get('user.uid')
            || (int)Yii::$app->session->get('user.ustatus') === 3
        ) {
            $permitted_types = [1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>12, 7=>13];
            $types = Message::getMessageDirectionsArray($permitted_types);

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Message successfully updated!'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to update message!'));
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'types' => $types,
                    'userInfoBlock' => User::getUserInfoBlock()
                ]);
            }
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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
                if (!empty($message)) {
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
        if (Yii::$app->request->get('id')) {
            $message = $this->findModel($id);
            if (!empty($message)) {
                $file = (new \yii\db\Query())
                ->select('id as mid, name as mname, files as mfile, data as mdate')
                ->from('calc_message')
                ->where([
                    'id' => Yii::$app->request->get('id'),
                    'send' => 0
                ])
                ->one();

                $model = new UploadForm();

                if (Yii::$app->request->isPost) {
                    $model->file = UploadedFile::getInstance($model, 'file');
                    if ($model->file && $model->validate()) {
                        //задаем адрес папки с файлами для поиска
                        $spath = "uploads/calc_message/";
                        //задаем адрес папки для загрузки файла
                        $filepath = "uploads/calc_message/".$id."/fls/";
                        //задаем имя файла
                        $filename = "file-".$id.".".$model->file->extension;
                        //проверяем наличие файла и папки
                        $filesearch = FileHelper::findFiles($spath,['only'=>[$filename]]);
                        if (empty($filesearch)) {
                            FileHelper::createDirectory($spath.$id."/");
                            FileHelper::createDirectory($spath.$id."/fls/");
                        }
                        $model->file->saveAs($filepath.$filename);
                        $db = (new \yii\db\Query())
                        ->createCommand()
                        ->update('calc_message', [
                            'files' => $filename,
                            'data' => $file['mdate']
                        ], [
                            'id' => $id
                        ])
                        ->execute();
                        return $this->redirect(['message/upload', 'id' => $id]);
                    }
                }
                return $this->render('upload', [
                    'model' => $model,
                    'file' => $file,
                    'userInfoBlock' => User::getUserInfoBlock()
                ]);
            } else {
                throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));
            }
        } else {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required arguments: { id }'));
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
        
        if(
            !empty($mess)
            && (
                (int)$mess['sender'] === (int)Yii::$app->session->get('user.uid')
                || (int)Yii::$app->session->get('user.ustatus') === 3
            )
        ) {
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
        return $this->redirect(['index']);
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
}
