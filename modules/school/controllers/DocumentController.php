<?php

namespace app\modules\school\controllers;

use app\models\AccessRule;
use app\modules\school\models\File;
use app\modules\school\models\User;
use app\modules\school\School;
use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
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
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'upload' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param Action $action
     * @return bool
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     */
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
        return $this->render('index', [
            'fileList' => File::find()->andWhere([
                'entity_type' => File::TYPE_DOCUMENTS,
                'module_type' => School::MODULE_NAME,
            ])->all(),
            'uploadForm' => new UploadForm(),
        ]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDownload(int $id)
    {
        /** @var File|null $file */
        $file = File::find()->andWhere(['id' => $id, 'module_type' => School::MODULE_NAME])->one();
        if (empty($file)) {
            throw new NotFoundHttpException(Yii::t('app', 'File not found!'));
        }

        return Yii::$app->response->sendFile($file->getPath(), $file->original_name, ['inline' => true]);
    }

    /**
     * @return mixed
     *
     * @throws ForbiddenHttpException
     * @throws \yii\base\Exception
     */
    public function actionUpload()
    {
        $model = new UploadForm();
        $model->file = UploadedFile::getInstance($model, 'file');
        if ($model->file && $model->validate()) {
            if ($model->saveFile(File::getTempDirPath())) {
                $file = new File([
                    'file_name'     => $model->file_name,
                    'original_name' => $model->original_name,
                    'size'          => $model->file->size,
                ]);
                if ($file->save()) {
                    $file->setEntity(File::TYPE_DOCUMENTS);
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'File successfully uploaded!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to upload file!'));
            }
            return $this->redirect(['document/index']);
        }
        throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id)
    {
        $file = File::find()->andWhere(['id' => $id, 'module_type' => School::MODULE_NAME])->one();
        if (empty($file)) {
            throw new NotFoundHttpException(Yii::t('app', 'File not found!'));
        }
        $errorMessage = Yii::t('app', 'Failed to delete file!');
        try {
            if ($file->delete()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'File successfully deleted!'));
            } else {
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $errorMessage);
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $errorMessage);
        }

        return $this->redirect(['document/index']);
    }
}