<?php

namespace app\modules\school\controllers;

use app\models\Book;

use app\models\Groupteacher;
use app\models\GroupBook;
use app\modules\school\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * GroupBookController implements the CRUD actions for GroupBook model.
 */
class GroupBookController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $rules = ['create', 'delete', 'primary'];
        return [
	        'access' => [
                'class' => AccessControl::class,
                'only'  => $rules,
                'rules' => [
                    [
                        'actions' => $rules,
                        'allow'   => false,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => $rules,
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete'      => ['post'],
                    'set-primary' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Добавляет учебник в группу
     * @param int $gid
     * 
     * @return mixed
     */
    public function actionCreate($gid)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 5, 6, 10])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        /** @var Groupteacher $group */
        $group = Groupteacher::find()->andWhere(['id' => $gid])->one();
        if (empty($group)) {
            throw new NotFoundHttpException("Группа №{$gid} не найдена.");
        }
        $params['gid'] = $gid;
        $params['active'] = $group->visible ?? null;

        $language   = $group->language;
        $groupBooks = $group->groupBooks;
        $bookIds    = ArrayHelper::getColumn($groupBooks, 'book_id');

        $books = (new \yii\db\Query())
            ->select(['id' => 'b.id', 'name' => 'b.name'])
            ->from(['b' => Book::tableName()])
            ->where([
                'b.language_id' => $language['id'],
                'b.visible'     => 1
            ])
            ->andFilterWhere(['not in', 'b.id', $bookIds])
            ->orderby(['b.name' => SORT_ASC])
            ->all();

        $books = ArrayHelper::map($books, 'id', 'name');
 
        $model = new GroupBook(['group_id' => $gid]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->saveWithPrimaryCheck()) {
                Yii::$app->session->setFlash('success', 'Учебник успешно добавлен в группу.');
                return $this->redirect(['group-book/create', 'gid' => $gid]);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить учебник в группу.');
            }
        }
        return $this->render('create', [
            'model'         => $model,
            'books'         => $books,
            'group'         => $group,
            'groupBooks'    => $groupBooks,
            'userInfoBlock' => User::getUserInfoBlock(),
            'params'        => $params,
        ]);
    }

    /**
     * Убирает учебник из группы
     * @param integer $id
     * 
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $gid   = $model->group_id; 

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Учебник успешно удален из группы.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить учебник из группы.');
        }

        return $this->redirect(['group-book/create', 'gid' => $gid]);
    }

    /**
     * Устанавливает основной учебник группы
     * @param int $id
     * 
     */
    public function actionPrimary($id) {
        $model = $this->findModel($id);

        if (GroupBook::setPrimary($model)) {
            Yii::$app->session->setFlash('success', 'Учебник успешно установлен основным для группы.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось установить учебник основным для группы.');
        }

        return $this->redirect(['group-book/create', 'gid' => $model->group_id]);
    }

    /**
     * Finds the GroupBook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GroupBook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GroupBook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
