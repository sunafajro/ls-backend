<?php

namespace app\modules\school\controllers;

use app\models\File;
use Yii;
use app\models\Message;
use app\models\Student;
use app\models\Teacher;
use app\models\UploadForm;
use app\modules\school\models\User;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    /** @inheritDoc */
    public function behaviors()
    {
        $rules = ['index','view','create','update','delete','response','upload','send','ajaxgroup'];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => $rules,
                'rules' => [
                    [
                        'actions' => $rules,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $rules,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete'    => ['post'],
                    'ajaxgroup' => ['post'],
                    'response'  => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(string $month = NULL, string $year = NULL)
    {
        if ($month) {
            if (($month < 1 && $month > 12) || $month === 'all')  {
                $month = NULL;
            }
        } else {
            $month = date('n');
            $month = $month < 10 ? '0' . $month : $month;
        }

        if ($year) {
            if (($year < 2012 && $year > $year) || $year === 'all') {
                $year = NULL;
            }
        } else {
            $year = date('Y');
        }

        $unreaded = Message::getUnreadedMessagesIds();

        $start = NULL;
        $end = NULL;
        if ($month && $year) {
            $start = $year . '-' . $month . '-01';
            $end = date('Y-m-t', strtotime($start));
        }

        $incomingRaw = Message::getUserMessages($start, $end, 'in');
        $outcoming = Message::getUserMessages($start, $end, 'out');

        $outcomingIds = [];
        foreach ($outcoming as &$out) {
            $outcomingIds[] = $out['id'];
            $out['direction'] = 'out';
        }
        $incoming = [];
        foreach ($incomingRaw as $in) {
            if (!in_array($in['id'], $outcomingIds)) {
                $in['direction'] = 'in';
                $incoming[] = $in;
            }
        }
        $messages = array_merge($incoming, $outcoming);

        $messagesReaded = !empty($outcomingIds) ? Message::getMessagesReadStatus($outcomingIds, 1) : [];
        $messagesAll = !empty($outcomingIds) ? Message::getMessagesReadStatus($outcomingIds, NULL) : [];

        return $this->render('index', [
            'messages'       => $messages,
            'messagesAll'    => $messagesAll,
            'messagesReaded' => $messagesReaded,
            'month'          => $month,
            'unreaded'       => $unreaded,
            'userInfoBlock'  => User::getUserInfoBlock(),
            'year'           => $year,
        ]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $message = Message::getMessageById($id);
        if (!$message) {
            throw new NotFoundHttpException(Yii::t('yii', 'The requested page does not exist.'));
        }
        if (
            in_array((int)Yii::$app->user->identity->id, [$message['sender_id'], $message['reciever_id']])
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

    /**
     * @param int|null $target_type
     * @param int|null $target_id
     * 
     * @return mixed
     */
    public function actionCreate(int $target_type = null, int $target_id = null)
    {
        $types = Message::getMessageDirectionsArray(Message::MESSAGE_ENABLED_TYPES);
        $model = new Message();
        if ($target_type) {
            $model->calc_messwhomtype = $target_type;
            if ($target_id) {
                $model->refinement_id = $target_id;
                $receivers = $target_type == 5
                    ? User::find()->where(['visible' => 1])->all()
                    : Student::find()->where(['visible' => 1])->all();
            }
        }
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($target_type && ((int)$target_type !== (int)$model->calc_messwhomtype)) {
                $model->calc_messwhomtype = $target_type;
            }
            if ($target_id && ((int)$target_id !== (int)$model->refinement_id)) {
                $model->calc_messwhomtype = $target_id;
            }
            $files = [];
            if (is_array($model->files) && !empty($model->files)) {
                $files = $model->files;
                $model->files = null;
            }
            if ($model->save()) {
                foreach ($files ?? [] as $fileId) {
                    $file = File::find()->andWhere(['id' => $fileId])->one();
                    $file->setEntity(File::TYPE_ATTACHMENTS, $model->id);
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Message successfully created!'));
                if (Yii::$app->request->post('send', null)) {
                    return $this->redirect(['message/send', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to create message!'));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model'         => $model,
                'types'         => $types,
                'receivers'     => ArrayHelper::map($receivers ?? [], 'id', 'name'),
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        if (
            (int)$model->user === (int)Yii::$app->user->identity->id
            || (int)Yii::$app->session->get('user.ustatus') === 3
        ) {
            $types = Message::getMessageDirectionsArray(Message::MESSAGE_ENABLED_TYPES);
            if (in_array($model->calc_messwhomtype, [5, 13])) {
                $receivers = $model->calc_messwhomtype == 5
                    ? User::find()->where(['visible' => 1])->all()
                    : Student::find()->where(['visible' => 1])->all();
            }
            $files = [];
            $fileString = $model->files;
            if ($model->load(Yii::$app->request->post())) {
                if (is_array($model->files) && !empty($model->files)) {
                    $files = $model->files;
                    $model->files = $fileString;
                }
                if ($model->save(true, ['name', 'description'])) {
                    foreach ($files ?? [] as $fileId) {
                        $file = File::find()->andWhere(['id' => $fileId, 'entity_type' => File::TYPE_TEMP, 'entity_id' => null])->one();
                        if ($file) {
                            $file->setEntity(File::TYPE_ATTACHMENTS, $model->id);
                        }
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Message successfully updated!'));
                    if (Yii::$app->request->post('send', null)) {
                        return $this->redirect(['message/send', 'id' => $model->id]);
                    }
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to update message!'));
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model'         => $model,
                    'receivers'     => ArrayHelper::map($receivers ?? [], 'id', 'name'),
                    'types'         => $types,
                    'userInfoBlock' => User::getUserInfoBlock()
                ]);
            }
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        if (
            (int)$model->user === (int)Yii::$app->user->identity->id
            || (int)Yii::$app->session->get('user.ustatus') === 3
        ) {
            try {
                if ($model->delete()) {
                    Yii::$app->session->setFlash('success', 'Сообщение успешно удалено!');
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось удалить сообщение!');
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Не удалось удалить сообщение!');
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', 'Не удалось удалить сообщение!');
            }

            return $this->redirect(['message/index']);
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }        
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionResponse(int $id)
    {
        $messageTable = Message::tableName();
        $reportTable = 'calc_messreport';
        $toResponse = Yii::$app->request->post('toResponse', false);
        /** @var Message $message */
        $message = Message::find()
            ->innerJoin($reportTable, "{$reportTable}.calc_message = {$messageTable}.id")
            ->where([
                "{$messageTable}.id"  => $id,
                "{$reportTable}.user" => Yii::$app->user->identity->id,
            ])
            ->one();

        /* если есть непрочитанное сообщение меняем его статус на прочитанное */
        if (!empty($message)) {
            $target = $message->calc_messwhomtype;
            try {
                $db = (new \yii\db\Query())
                ->createCommand()
                ->update(
                    'calc_messreport',
                    ['ok' => '1'],
                    [
                        'calc_message' => $id,
                        'user'         => Yii::$app->user->identity->id
                    ])
                ->execute();
                if ($toResponse && in_array($target, [5, 100])) {
                    return $this->redirect(['message/create', 'target_type' => $target == 100 ? 13 : 5, 'target_id' => $message->user]);
                } else {
                    Yii::$app->session->setFlash('success', 'Сообщение успешно отмечено прочтенным!');
                }
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', 'Не удалось отметить сообщение прочтенным!');
            }
        } else {
            throw new NotFoundHttpException('Сообщение №' . $id . ' не найдено.');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpload(int $id)
    {
        $message = $this->findModel($id);
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $spath = Yii::getAlias('@uploads/calc_message');
                $filename = $model->resizeAndSave($spath, $id, 'fls');
                $message->files = $filename;
                $message->save(true, ['files']);
                return $this->redirect(['message/upload', 'id' => $id]);
            }
        }
        return $this->render('upload', [
            'model' => $model,
            'message' => $message,
            'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }
    
    public function actionSend(int $id)
    {
        $userId     = (int)Yii::$app->user->identity->id;
        $userRoleId = (int)Yii::$app->session->get('user.ustatus');
        $message    = $this->findModel($id);
    
        if (((int)$message['user'] === $userId || $userRoleId === 3) && (int)$message->send === 0) {
            $condition = [
                'calc_message' => $message->id,
                'user'         => 0,
                'ok'           => 0,
                'data'         => date('Y-m-d H:i:s'),
                'send'         => 1
            ];
            if (in_array($message->calc_messwhomtype, [5, 13])) {
                $condition['user'] = $message->refinement_id;
                $report = (new \yii\db\Query())
                ->createCommand()
                ->insert('calc_messreport', $condition)
                ->execute();
            } else if (in_array($message->calc_messwhomtype, [1, 2, 3, 4])) {
                $recievers = [];
                $query = (new \yii\db\Query())
                ->select(['id' => 'u.id'])
                ->from(['u' => User::tableName()])
                ->where(['u.visible' => 1]);
                switch($message->calc_messwhomtype) {
                    case 2:
                        $query = $query->andWhere(['u.status' => 3]);
                        break;
                   case 3:
                        $query = $query->andWhere(['u.status' => 4]);
                        break;
                   case 4:
                        $query = $query->innerJoin(['t' => Teacher::tableName()], 't.id = u.calc_teacher')
                        ->andWhere(['t.visible' => 1]);
                        break;
                }
                $recievers = $query->all();
                if (!empty($recievers)) {
                    foreach ($recievers as $r) {
                        $condition['user'] = $r['id'];
                        $report = (new \yii\db\Query())
                        ->createCommand()
                        ->insert('calc_messreport', $condition)
                        ->execute();
                    }
                }
            }            
            $message->send = 1;
            if ($message->save(true, ['send'])) {
                Yii::$app->session->setFlash('success', 'Сообщение успешно отправлено!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось отправить сообщение!');
            }
        } else {
            throw new NotFoundHttpException("Сообщение №{$id} не найдено или уже отправлено.");
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

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
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
