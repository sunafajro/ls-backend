<?php

namespace app\modules\school\controllers;

use app\models\AccessRule;
use app\modules\school\models\Auth;
use app\modules\school\models\Document;
use app\modules\school\models\search\DocumentSearch;
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

/**
 * Class DocumentController
 * @package app\modules\school\controllers
 */
class DocumentController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        $rules = ['index','download','upload','delete'];
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
     * @param Action $action
     * @return bool
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
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

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'uploadForm' => new UploadForm(),
        ]);
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
            if ($model->saveFile(Document::getTempDirPath())) {
                $file = new Document([
                    'file_name'     => $model->file_name,
                    'original_name' => $model->original_name,
                    'size'          => $model->file->size,
                ]);
                if ($file->save()) {
                    $file->setEntity(Document::DEFAULT_ENTITY_TYPE);
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
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete(string $id)
    {
        /** @var Auth $user */
        $user   = Yii::$app->user->identity;
        $roleId = $user->roleId;
        $userId = $user->id;

        $file = $this->findModel(intval($id));
        $errorMessage = Yii::t('app', 'Failed to delete file!');
        try {
            if (!in_array($roleId, [3]) && $file->user_id !== $userId) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            if ($file->delete()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'File successfully deleted!'));
            } else {
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $errorMessage);
        }

        return $this->redirect(['document/index']);
    }

    /**
     * @param int $id
     * @return Document
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Document
    {
        if (($model = Document::find()->byId($id)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'File not found!'));
        }
    }
}