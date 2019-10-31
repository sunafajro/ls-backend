<?php

namespace app\controllers;

use Yii;
use app\models\Groupteacher;
use app\models\GroupBook;
use app\models\Tool;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

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
        $rules = ['create', 'delete', 'set-primary'];
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
        $group = Groupteacher::find()->andWhere(['id' => $gid])->one();
        if (empty($group)) {
            throw new NotFoundHttpException("Группа №{$gid} не найдена.");
        }
        $params['gid'] = $gid;
        $params['active'] = Groupteacher::getGroupStateById($gid);

        if($gid) {
            $language = (new \yii\db\Query())
            ->select('l.id as id')
            ->from('calc_lang l')
            ->leftJoin('calc_service s', 's.calc_lang=l.id')
            ->leftJoin('calc_groupteacher gt', 'gt.calc_service=s.id')
            ->where('gt.id=:gid', [':gid'=>$gid])
            ->one();
        } else {
            $language = [];
        }

        $curr_books = (new \yii\db\Query())
        ->select('gtb.id as id, b.name as name, gtb.primary as prime, b.id as bid')
        ->from(['gtb' => GroupBook::tableName()])
        ->innerJoin('books b', 'b.id = gtb.book_id')
        ->where('gtb.group_id=:gid', [':gid' => $gid])
        ->orderby(['gtb.primary' => SORT_DESC, 'b.name' => SORT_ASC])
        ->all();

        if(!empty($curr_books)) {
            foreach($curr_books as $cb) {
                $bids[] = $cb['bid'];
            }
        } else {
            $bids = NULL;
        }

        if(!empty($language)) {
            $books_tmp = (new \yii\db\Query())
            ->select('b.id as id, b.name as name')
            ->from('books b')
            ->where('b.language_id=:lang and b.visible=:one', [':lang'=>$language['id'], ':one' => 1])
            ->andFilterWhere(['not in', 'b.id', $bids])
            ->orderby(['b.name'=>SORT_ASC])
            ->all();
            
            if(!empty($books_tmp)) {
                foreach($books_tmp as $b) {
                    $books[$b['id']] = $b['name'];
                }
            } else {
                $books = [];
            }
        } else {
            $books = [];
        }
 
        $model = new GroupBook();

        if ($model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->calc_groupteacher = $gid;
            if($model->prime != 0) {
                $book = GroupBook::find()->where('primary=:one', [':one'=>1])->one();
                if($book !== NULL) {
                    $book->prime = 0;
                    $book->save();
                }
            }
            $model->save();
            return $this->redirect(['group-book/create', 'gid' => $gid]);
        } else {
            return $this->render('create', [
                'model'         => $model,
                'books'         => $books,
                'curr_books'    => $curr_books,
                'userInfoBlock' => User::getUserInfoBlock(),
                'groupinfo'     => $group->getInfo(),
                'items'         => Groupteacher::getMenuItemList($gid, Yii::$app->controller->id . '/' . Yii::$app->controller->action->id),
                'params'        => $params,
            ]);
        }
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
