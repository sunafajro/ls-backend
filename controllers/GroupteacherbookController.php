<?php

namespace app\controllers;

use Yii;
use app\models\Groupteacher;
use app\models\Groupteacherbook;
use app\models\Tool;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * GroupteacherbookController implements the CRUD actions for Groupteacherbook model.
 */
class GroupteacherbookController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
	        'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','delete', 'primary'],
                'rules' => [
                    [
                        'actions' => ['create','delete', 'primary'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','delete', 'primary'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Creates a new Groupteacherbook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($gid)
    {
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
        ->select('gtb.id as id, b.name as name, gtb.prime as prime, b.id as bid')
        ->from('calc_groupteacherbook gtb')
        ->leftJoin('calc_schoolbook b', 'b.id=gtb.calc_book')
        ->where('gtb.visible=:one and gtb.calc_groupteacher=:gid', [':one' => 1, ':gid' => $gid])
        ->orderby(['gtb.prime'=>SORT_DESC, 'b.name' => SORT_ASC])
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
            ->from('calc_schoolbook b')
            ->where('b.calc_lang=:lang and b.visible=:one', [':lang'=>$language['id'], ':one' => 1])
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
 
        $model = new Groupteacherbook();

        if ($model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->calc_groupteacher = $gid;
            if($model->prime != 0) {
                $book = Groupteacherbook::find()->where('visible=:one and prime=:one', [':one'=>1])->one();
                if($book !== NULL) {
                    $book->prime = 0;
                    $book->save();
                }
            }
            $model->save();
            return $this->redirect(['groupteacherbook/create', 'gid' => $gid]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'books' => $books,
                'curr_books' => $curr_books,
                'userInfoBlock' => User::getUserInfoBlock(),
                'groupinfo' => Groupteacher::getGroupInfoById($gid),
                'items' => Groupteacher::getMenuItemList($gid, Yii::$app->controller->id . '/' . Yii::$app->controller->action->id),
                'params' => $params,
            ]);
        }
    }

    /**
     * Deletes an existing Groupteacherbook model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
	
            if($model->visible == 1) {
                $model->visible = 0;
                $model->save();
            }

        return $this->redirect(['groupteacherbook/create', 'gid' => $model->calc_groupteacher]);
    }

    public function actionPrimary($id) {
        $model = $this->findModel($id);
        if($model->prime == 0) {
            $book = Groupteacherbook::find()->where('visible=:one and prime=:one', [':one'=>1])->one();
            if($book !== NULL) {
                $book->prime = 0;
                $book->save();
            }
            $model->prime = 1;
            $model->save();
        }
        return $this->redirect(['groupteacherbook/create', 'gid' => $model->calc_groupteacher]);
    }

    /**
     * Finds the Groupteacherbook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Groupteacherbook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Groupteacherbook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
