<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\models\UploadForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class DocumentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','download','upload','delete'],
                'rules' => [
                    [
                        'actions' => ['index','download','upload','delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','download','upload','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'upload' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
		if(parent::beforeAction($action)) {
            $rule = new AccessRule();
			if ($rule->checkAccess($action->controller->id, $action->id) === false) {
				throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
			}
			return true;
		} else {
			return false;
		}
    }

    public function actionIndex()
    {
        $path = Yii::getAlias('@app/data/files/');
        $files = scandir($path);
        $fileList = [];
        foreach($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== '.gitkeep') {
                $fileList[] = [
                    'fileName' => $file,
                    'fileHash' => md5($path . $file),
                ];
            }
        }
        return $this->render('index', [
            'fileList'      => $fileList,
            'uploadForm'    => new UploadForm(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    public function actionDownload(string $id)
    {
        $path = Yii::getAlias('@app/data/files/');
        $files = scandir($path);
        $fileName = '';
        foreach($files as $file) {
            $tmpFilePath = md5($path . $file);
            if ($file !== '.' && $file !== '..' && $file !== '.gitkeep') {
                if ($tmpFilePath === $id) {
                    $fileName = $file;
                }
            }
        }
        if ($fileName) {
            return Yii::$app->response->sendFile($path . $fileName, $fileName);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'File not found!'));
        }
    }

    public function actionUpload()
    {
        $model = new UploadForm();
        $model->file = UploadedFile::getInstance($model, 'file');
        if ($model->file && $model->validate()) {
            $path = Yii::getAlias('@app/data/files/');
            if ($model->saveFile($path)) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'File successfully uploaded!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to upload file!'));
            }
            return $this->redirect(['document/index']);
        }
        throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
    }

    public function actionDelete($id)
    {
        $path = Yii::getAlias('@app/data/files/');
        $files = scandir($path);
        $filePath = '';
        foreach($files as $file) {
            $tmpFilePath = md5($path . $file);
            if ($file !== '.' && $file !== '..' && $file !== '.gitkeep') {
                if ($tmpFilePath === $id) {
                    $filePath = $path . $file;
                }
            }
        }
        if ($filePath) {
            if (unlink($filePath)) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'File successfully deleted!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete file!'));
            }
            return $this->redirect('index');
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'File not found!'));
        }
    }
}