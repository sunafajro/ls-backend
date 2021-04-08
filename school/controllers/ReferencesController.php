<?php

namespace school\controllers;

use school\models\AccessRule;
use Yii;
use common\components\helpers\JsonResponse;
use school\models\City;
use school\models\Edunorm;
use school\models\Language;
use school\models\LanguagePremium;
use school\models\Office;
use school\models\Phonebook;
use school\models\Reference;
use school\models\Room;
use school\models\Studnorm;
use school\models\Timenorm;
use school\models\Volonteer;
use school\models\Coefficient;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Class ReferencesController
 * @package school\controllers
 */
class ReferencesController extends Controller
{
    public function behaviors(): array
    {
        $actions = [
            'index',
            'app-menu-links',
            'app-list',
            'app-create',
            'app-delete',
        ];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => $actions,
                'rules' => [
                    [
                        'actions' => $actions,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $actions,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'app-create' => ['post'],
                    'app-delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $name = Yii::$app->request->get('name', null);
            $controllerId = $action->controller->id;
            $actionId = str_replace('app-', '', $action->id);
            if ($name !== NULL && in_array($actionId, ['create', 'delete', 'list'])) {
                $controllerId = "{$controllerId}/{$name}";
            }
            if (AccessRule::checkAccess("{$controllerId}_{$actionId}") == false) {
                throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
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
        return $this->render('index');
    }

    /**
     * @return mixed
     */
    public function actionAppMenuLinks()
    {
        return $this->asJson([
            'status' => true,
            'links'  => Reference::getLinks()
        ]);
    }

    /**
     * @return mixed
     */
    public function actionAppList($name = 'phonebook')
    {
        ['columns' => $columns, 'data' => $data] = $this->getEntities($name, 'list');

        return $this->asJson([
            'actions' => AccessRule::getCRUD("references/{$name}"),
            'columns' => $columns,
            'data'    => $data,
            'status'  => true
        ]);
    }

    public function actionAppCreate($name)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        ['entityName' => $entityName, 'model' => $model] = $this->getEntities($name, 'entity');
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return JsonResponse::ok(true, Yii::t('app', "{$entityName} successfully created!"));
            } else {
                return JsonResponse::internalServerError(Yii::t('app', "{$entityName} create failed!"));
            }            
        } else {
            return JsonResponse::internalServerError(Yii::t('app', 'Error loading model.'));
        }
    }

    public function actionAppDelete($name, $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        ['entityName' => $entityName, 'model' => $model] = $this->getEntities($name, 'entity', $id);
        if ($model) {
            if ($model->delete()) {
                return JsonResponse::ok(true, Yii::t('app', "{$entityName} successfully deleted!"));
            } else {
                return JsonResponse::internalServerError(Yii::t('app', "{$entityName}  delete failed!"));
            } 
        } else {
            return JsonResponse::objectNotFound();
        }
    }

    protected function getEntities(string $name, string $type = 'list', int $id = null) : array
    {
        $condition = ['id' => $id, 'visible' => 1];
        switch ($name) {
            case Reference::TYPE_CITIES:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = City::getCitiesList();
                } else {
                    $model = $id ? City::find()->andWhere($condition)->one() : new City();
                    $entityName = 'City';
                }
                break;
            case Reference::TYPE_COEFFICIENTS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Coefficient::getCoefficientsList();    
                } else {
                    $model = $id ? Coefficient::find()->andWhere($condition)->one() : new Coefficient();
                    $entityName = 'Coefficient';
                }
                break;
            case Reference::TYPE_LANGUAGES:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Language::getLanguages();    
                } else {
                    $model = $id ? Language::find()->andWhere($condition)->one() : new Language();
                    $entityName = 'Language';
                }
                break;
            case Reference::TYPE_OFFICES:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Office::getOfficesWithCitiesList();    
                } else {
                    $model = $id ? Office::find()->andWhere($condition)->one() : new Office();
                    $entityName = 'Office';
                }
                break;
            case Reference::TYPE_PREMIUMS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = LanguagePremium::getLanguagePremiums();    
                } else {
                    $model = $id ? LanguagePremium::find()->andWhere($condition)->one() : new LanguagePremium();
                    $entityName = 'Language premium';
                }
                break;
            case Reference::TYPE_ROOMS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Room::getRoomsList();    
                } else {
                    $model = $id ? Room::find()->andWhere($condition)->one() : new Room();
                    $entityName = 'Room';
                }
                break;
            case Reference::TYPE_STUDENT_NORMS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Studnorm::getPaynorms();    
                } else {
                    $model = $id ? Studnorm::find()->andWhere($condition)->one() : new Studnorm();
                    $entityName = 'Studnorm';
                }
                break;
            case Reference::TYPE_TEACHER_NORMS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Edunorm::getPaynorms();    
                } else {
                    $model = $id ? Edunorm::find()->andWhere($condition)->one() : new Edunorm();
                    $entityName = 'Teachernorm';
                }
                break;
            case Reference::TYPE_TIME_NORMS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Timenorm::getTimenorms();
                } else {
                    $model = $id ? Timenorm::find()->andWhere($condition)->one() : new Timenorm();
                    $entityName = 'Timenorm';
                }
                break;
            case Reference::TYPE_VOLONTEERS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Volonteer::getVolonteers();
                } else {
                    $model = $id ? Volonteer::find()->andWhere($condition)->one() : new Volonteer();
                    $entityName = 'Volonteer';
                }
                break;
            default:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Phonebook::getPhoneList();
                } else {
                    $model = $id ? Phonebook::find()->andWhere($condition)->one() : new Phonebook();
                    $entityName = 'Contact';
                }
                break;
        }

        return $type === 'list'
            ? ['columns' => $columns, 'data' => $data]
            : ['entityName' => $entityName, 'model' => $model];
    }
}