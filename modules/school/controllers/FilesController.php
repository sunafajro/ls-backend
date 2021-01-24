<?php

namespace app\modules\school\controllers;

use app\models\UploadForm;
use app\modules\school\models\Auth;
use app\modules\school\models\MessageFile;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

// TODO перенести методы в MessageController
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
            if ($model->saveFile(MessageFile::getTempDirPath())) {
                $file = new MessageFile([
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

        return $this->asJson($result);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDownload(string $id)
    {
        $file = $this->findModel(intval($id));
        // TODO должно быть доступно только администратору, загрузившему файл и адресату сообщения
        return Yii::$app->response->sendFile($file->getPath(), $file->original_name, ['inline' => true]);
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
        /** @var Auth $user */
        $user = Yii::$app->user->identity;
        if ($file->user_id === $user->id) {
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
     * @return MessageFile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): MessageFile
    {
        /** @var MessageFile $model */
        if (($model = MessageFile::find()->byId($id)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}