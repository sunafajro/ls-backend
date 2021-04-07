<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use school\models\Auth;
use school\models\FileLink;
use school\models\searches\FileLinkSearch;
use \Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class FileLinkController
 * @package school\controllers
 */
class FileLinkController extends BaseController
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        $rules = ['index','create','delete'];
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
                    'create' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'main-2-column';
        $searchModel = new FileLinkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => new FileLink(),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FileLink();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Ссылка на внешний ресурс успешно добавлена!');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось добавить ссылку на внешний ресурс!');
        }

        return $this->redirect(['file-link/index']);
    }

    /**
     * @param string $id
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
        $errorMessage = 'Не удалось удалить ссылку на внешний ресурс!';
        try {
            if (!in_array($roleId, [3]) && $file->user_id !== $userId) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            if ($file->delete(false)) {
                Yii::$app->session->setFlash('success', 'Ссылка на внешний ресурс успешно удалена!');
            } else {
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $errorMessage);
        }

        return $this->redirect(['file-link/index']);
    }

    /**
     * @param int $id
     * @return FileLink
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): FileLink
    {
        if (($model = FileLink::find()->byId($id)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'File not found!'));
        }
    }
}