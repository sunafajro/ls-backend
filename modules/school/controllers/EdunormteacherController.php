<?php

namespace app\modules\school\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Edunormteacher;
use app\models\Teacher;
use app\models\Tool;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * EdunormteacherController implements the CRUD actions for Edunormteacher model.
 */
class EdunormteacherController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(parent::beforeAction($action)) {
            if (User::checkAccess($action->controller->id, $action->id) == false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }
    

    /**
     * Метод позволяет руководителю
     * добавить преподавателю ставку
     */
    public function actionCreate($tid)
    {
        $userInfoBlock = User::getUserInfoBlock();
        $teacherTaxes = Edunormteacher::find()
        ->where('calc_teacher=:tid AND active=:vis AND visible=:vis', 
        [':tid' => $tid, ':vis'=> 1])->all();

        $taxesIds = NULL;
        if ($teacherTaxes !== NULL) {
            foreach($teacherTaxes as $t) {
                switch($t->company) {
                    case 1: 
                        $taxesIds[] = $t->calc_edunorm;
                        break;
                    case 2:
                        $taxesIds[] = $t->calc_edunorm;
                        break;
                }
            }
        }

        $temp_norms = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_edunorm')
        ->where('visible=:vis', [':vis'=>1])
        ->andFilterWhere(['not in','id', $taxesIds])
        ->all();
        
        $norms = [];
        foreach($temp_norms as $tn) {
            $norms[$tn['id']] = $tn['name'];
        }
        unset($temp_norms);

        $tnorms = (new \yii\db\Query())
        ->select([
            'enid' => 'ent.id',
            'nid' => 'en1.id',
            'nname' => 'en1.name',
            'ndid' => 'en2.name',
            'ndname' => 'en2.name',
            'ndate' => 'ent.data',
            'active' => 'ent.active',
            'tjplace' => 'ent.company'
        ])
        ->from(['ent' => 'calc_edunormteacher'])
        ->innerjoin(['en1' => 'calc_edunorm'], 'en1.id = ent.calc_edunorm')
        ->leftjoin(['en2' => 'calc_edunorm'], 'en2.id = ent.calc_edunorm_day')
        ->where([
            'ent.visible' => 1,
            'ent.calc_teacher' => $tid
        ])
        ->orderby(['ent.id'=>SORT_DESC])
        ->all();

        // генерируем новую модель
        $model = new Edunormteacher();

        $teacher = Teacher::findOne($tid);

        if ($model->load(Yii::$app->request->post())) {
            $model->calc_teacher = $tid;
            $model->data = date('Y-m-d');
            $model->visible = 1;
            $model->active = 1;
            $oldtax = Edunormteacher::find()
            ->where('calc_teacher=:tid AND active=:vis AND visible=:vis AND company=:place', 
            [':tid' => $tid, ':vis'=> 1, ':place' => $model->company])->one();
            if($model->save()) {
                /* деактивируем старую ставку */
                if($oldtax !== NULL) {
                    $oldtax->active = 0;
                    $oldtax->save();
                }
                /* сообщение об успехе */
                Yii::$app->session->setFlash('success', 'Ставка успешно добавлена!');
            } else {
                /* сообщение об ошибке */
                Yii::$app->session->setFlash('error', 'Не удалось добавить ставку!');
            }
            return $this->redirect(['edunormteacher/create', 'tid' => $tid]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'norms' => $norms,
                'tnorms' => $tnorms,
                'teacher' => $teacher,
                'userInfoBlock' => $userInfoBlock,
                'jobPlace' => [ 1 => 'ШИЯ', 2 => 'СРР' ]
        ]);
        }
    }

    public function actionDelete($id, $tid)
    {
        if (($model = Edunormteacher::findOne($id)) !== NULL) {
            $model->visible = 0;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app','Teacher tax successfully removed!'));
            } else {
                Yii::$app->session->setFlash('success', Yii::t('app','Failed to remove teacher tax!'));
            }
        }

        return $this->redirect(['create', 'tid' => $tid]);
    }
}
