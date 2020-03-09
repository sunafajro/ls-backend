<?php

namespace app\controllers;

use Yii;
use app\components\helpers\JsonResponse;
use app\models\AccessRule;
use app\models\City;
use app\models\Coefficient;
use app\models\Language;
use app\models\Office;
use app\models\Phonebook;
use app\models\Reference;
use app\models\Room;
use app\models\Volonteer;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class ReferencesController extends Controller
{
    const TYPE_CITIES       = 'cities';
    const TYPE_COEFFICIENTS = 'coefficients';
    const TYPE_CONTACTS     = 'contacts';
    const TYPE_LANGUAGES    = 'languages';
    const TYPE_OFFICES      = 'offices';
    const TYPE_ROOMS        = 'rooms';
    const TYPE_VOLONTEERS   = 'volonteers';

    public function behaviors()
    {
        $actions = [
            'index',
            'api-menu-links',
            'api-list',
            'api-create',
            'api-delete',
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
                    'create' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(parent::beforeAction($action)) {
            $name = Yii::$app->request->get('name', null);
            $controllerId = $action->controller->id;
            $actionId = str_replace('api-', '', $action->id);
            if ($name !== NULL && in_array($actionId, ['create', 'delete', 'list'])) {
                $controllerId = "{$controllerId}/{$name}";
            }
            if (AccessRule::checkAccess($controllerId, $actionId) == false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionApiMenuLinks()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'status' => true,
            'links'  => Reference::getItems()
        ];
    }

    public function actionApiList($name = 'phonebook')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        ['columns' => $columns, 'data' => $data] = $this->getEntities($name, 'list');

        return [
            'actions' => AccessRule::GetCRUD("references/{$name}"),
            'columns' => $columns,
            'data'    => $data,
            'status'  => true
        ];
    }

    public function actionApiCreate($name)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        ['entityName' => $entityName, 'model' => $model] = $this->getEntities($name, 'entity');
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return JsonResponse::ok(true, Yii::t('app', "{$entityName} successfully created!"));
            } else {
                var_dump($model->getErrors()); die();
                return JsonResponse::internalServerError(Yii::t('app', "{$entityName} create failed!"));
            }            
        } else {
            return JsonResponse::internalServerError(Yii::t('app', 'Error loading model.'));
        }
    }

    public function actionApiDelete($name, $id)
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
            case self::TYPE_CITIES:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = City::getCitiesList();
                } else {
                    $model = $id ? City::find()->andWhere($condition)->one() : new City();
                    $entityName = 'City';
                }
                break;
            case self::TYPE_COEFFICIENTS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Coefficient::getCoefficientsList();    
                } else {
                    $model = $id ? Coefficient::find()->andWhere($condition)->one() : new Coefficient();
                    $entityName = 'Coefficient';
                }
                break;
            case self::TYPE_LANGUAGES:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Language::getLanguages();    
                } else {
                    $model = $id ? Language::find()->andWhere($condition)->one() : new Language();
                    $entityName = 'Language';
                }
                break;
            case self::TYPE_OFFICES:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Office::getOfficesWithCitiesList();    
                } else {
                    $model = $id ? Office::find()->andWhere($condition)->one() : new Office();
                    $entityName = 'Office';
                }
                break;
            case self::TYPE_ROOMS:
                if ($type === 'list') {
                    ['columns' => $columns, 'data' => $data] = Room::getRoomsList();    
                } else {
                    $model = $id ? Room::find()->andWhere($condition)->one() : new Room();
                    $entityName = 'Room';
                }
                break;
            case self::TYPE_VOLONTEERS:
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