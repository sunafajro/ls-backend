<?php

namespace school\controllers;

use Yii;
use school\models\Langtranslator;
use school\models\Translation;
use school\models\Translationclient;
use school\models\Translationlang;
use school\models\Translationnorm;
use school\models\Translator;
use school\models\Tool;
use school\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

/** 
 *  Выполняет функцию обработки всех запросов к разделу Переводы
 */

class TranslateController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['translationlist','translatorlist','clientlist', 'languagelist', 'normlist'],
                'rules' => [
                    [
                        'actions' => ['translations','translators','clients', 'languages', 'norms'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['translations','translators','clients', 'languages', 'norms'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
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
     *  Метод выводит таблицу со списком переводов
     *  @return mixed
     */
     
    public function actionTranslations()
    {
        $params = [];
        $url_params = [
            'translate/tranlations',
            'TSS' => NULL,
            'LANG' => NULL,
            'MONTH' => date('m'),
            'YEAR' => date('Y'),
        ];
        
        $params = self::getUrlParams($url_params);
        
        return $this->render('translations', [
            'languages' => Translationlang::getLanguageListSimple(),
            'years' => Tool::getYearsSimple(),
            'translations'=> Translation::getTranslationList($params),
            'url_params' => $params
        ]);
    }
    
    /**
     *  Метод выводит таблицу со списком переводчиков
     *  @return mixed
     */
     
    public function actionTranslators()
    {
        $params = [];
        $url_params = [
            'translate/tranlators',
            'TSS' => NULL,
            'LANG' => NULL,
            'NOTAR' => NULL
        ];
        
        $params = self::getUrlParams($url_params);
        
        return $this->render('translators', [
            'userInfoBlock' => User::getUserInfoBlock(),
            'languages' => Translationlang::getLanguageListSimple(),
            'translator_languages' => Langtranslator::getTranslatorLanguageList(),
            'translators'=> Translator::getTranslatorList($params),
            'url_params' => $params
        ]);
    }
    
    public function actionClients()
    {
        $params = [];
        $url_params = [
            'translate/clients',
            'TSS' => NULL,
            'LANG' => NULL
        ];
        
        $params = self::getUrlParams($url_params);

        return $this->render('clients', [
            'userInfoBlock' => User::getUserInfoBlock(),
            'clients'=> Translationclient::getClientList($params),
            'url_params' => $params
        ]);
    }
    
    public function actionLanguages()
    {
        $params = [];
        $url_params = [
            'translate/languages',
            'TSS' => NULL
        ];

        $params = self::getUrlParams($url_params);
        $userInfoBlock = User::getUserInfoBlock();
        
        return $this->render('languages', [
            'userInfoBlock' => $userInfoBlock,
			'languages'=> Translationlang::getLanguageList($params),
            'url_params' => $params
        ]);
    }

    public function actionNorms()
    {
        $params = [];
        $url_params = [
            'translate/norms',
            'TSS' => NULL
        ];

        $params = self::getUrlParams($url_params);
        $userInfoBlock = User::getUserInfoBlock();
        
        return $this->render('norms', [
            'userInfoBlock' => $userInfoBlock,
			'norms'=> Translationnorm::getNormList($params),
            'url_params' => $params
        ]);
    }
    
    /**
     *  Метод заполняет массив значениями из GET запроса
     *  @return mixed
     */
    protected static function getUrlParams($url_params)
    {
        $params = $url_params;
        foreach($url_params as $key => $value) {
            if(Yii::$app->request->get($key) != 'all' && Yii::$app->request->get($key) != '') {
                $params[$key] = Yii::$app->request->get($key);
            }
        }
        return $params;
    }
}