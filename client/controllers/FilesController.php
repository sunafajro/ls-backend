<?php

namespace client\controllers;

use client\models\File;
use client\models\Message;
use client\models\UploadForm;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class FilesController
 * @package client\controllers
 */
class FilesController extends Controller
{
    /** @inheritDoc */
    public function behaviors()
    {
        $rules = ['upload','delete','download'];
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
                    'delete' => ['post'],
                    'upload' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function actionUpload()
    {
        $model = new UploadForm();
        $model->file = UploadedFile::getInstance($model, 'file');

        $result = [
            'success'  => false,
            'fileId'   => null,
            'fileName' => null,
        ];

        if ($model->file && $model->validate()) {
            if ($model->saveFile(File::getTempDirPath())) {
                $file = new File([
                    'file_name'     => $model->file_name,
                    'original_name' => $model->original_name,
                    'size'          => $model->file->size,
                ]);
                if ($file->save()) {
                    $result['success']  = true;
                    $result['fileId']   = $file->id;
                    $result['fileName'] = $file->original_name;
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDownload(string $id)
    {
        $uid = (int)Yii::$app->user->identity->id;
        $file = $this->findModel(intval($id));
        $result = false;
        if ($file->entity_type === File::TYPE_MESSAGE_FILES) {
            /** @var Message|null $message */
            $message = Message::find()->andWhere(['id' => $file->entity_id, 'visible' => 1])->one();
            if (!empty($message)) {
                $receiver = (new \yii\db\Query())->from(['r' => 'calc_messreport'])->andWhere(['r.user' => $uid, 'r.calc_message' => $message->id])->exists();
                if ((int)$message->calc_messwhomtype === 100 && (int)$message->user === $uid) {
                    // лк -> система учета
                    $result = true;
                } else if ((int)$message->calc_messwhomtype === 13 && $receiver) {
                    // система учета -> лк
                    $result = true;
                }
            }
        } else if ($file->entity_type === File::TYPE_GROUP_FILES) {
            $checkGroup = (new \yii\db\Query())
                ->select('*')
                ->from('calc_studgroup')
                ->andWhere([
                    'calc_groupteacher' => $file->entity_id,
                    'calc_studname' => $uid,
                ])->exists();
            if ($checkGroup) {
                $result = true;
            }
        } else if ($file->entity_type === File::TYPE_TEMP && (int)$file->user_id === $uid && file_exists($file->getPath())) {
            $result = true;
        }

        if ($result) {
            return Yii::$app->response->sendFile($file->getPath(), $file->original_name, ['inline' => true]);
        } else {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete(string $id)
    {
        $file = $this->findModel(intval($id));
        if ($file->entity_type === File::TYPE_TEMP && (int)$file->user_id === (int)Yii::$app->user->identity->id) {
            $result = ['success' => true];
            try {
                if (!$file->delete()) {
                    throw new Exception('Не удалось удалить файл!');
                }
            } catch (\Throwable $e) {
                $result['success'] = false;
            }
            return $this->asJson($result);
        } else {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * Finds the File model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return File the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = File::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}