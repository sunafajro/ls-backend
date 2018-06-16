<?php

namespace app\controllers;

use Yii;
use app\models\Langpremium;
use app\models\Teacher;
use app\models\Teacherlangpremium;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * LangteacherController implements the CRUD actions for CalcLangteacher model.
 */
class TeacherlangpremiumController extends Controller
{
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

    /* выводит список надбавок преподавателя и позволяет добавить новую */
    public function actionCreate($tid)
    {
        /* получаем текущие надбавки преподавателя */
        $teacher_premiums = Teacherlangpremium::getTeacherLangPremiums($tid);
        $params = NULL;
        if(!empty($teacher_premiums)) {
            foreach($teacher_premiums as $tlp) {
                $params[] = $tlp['lpid'];
            }
        }
        /* запрашиваем список надбавок из справочника за исключением уже назначенных */
        $premiums = Langpremium::getLangPremiumsSimple($params);
        
        $model = new Teacherlangpremium();
        if ($model->load(Yii::$app->request->post())) {
            if (($lpid = (int)$model->calc_langpremium) > 0) {
                Teacherlangpremium::removeDuplicateLangPremium($lpid, $tid);
                $model->calc_teacher = $tid;
                $model->user = Yii::$app->session->get('user.uid');
                $model->created_at = date('Y-m-d');
                $model->visible = 1;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', Yii::t('app','Language premium successfully added to the teacher!'));
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('app','Failed to add language premium to the teacher!'));
                }
            } else {
                Yii::$app->session->setFlash('success', Yii::t('app','Failed to add language premium to the teacher!'));
            }

            $this->redirect(['create', 'tid' => $tid]);
        }

        return $this->render('create', [
            'model' => $model,
            'teacher' => Teacher::findOne($tid),
            'premiums' => $premiums,
            'teacher_premiums' => $teacher_premiums,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    public function actionDelete($id, $tid)
    {
        if (($model = Teacherlangpremium::findOne($id)) !== NULL) {
            $model->visible = 0;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app','Teacher language premium successfully removed!'));
            } else {
                Yii::$app->session->setFlash('success', Yii::t('app','Failed to remove teacher language premium!'));
            }
        }

        return $this->redirect(['create', 'tid' => $tid]);
    }
}