<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Langtranslator;
use school\models\Translation;
use school\models\Translationclient;
use school\models\Translationlang;
use school\models\Translationnorm;
use school\models\Translator;
use school\models\Tool;
use yii\filters\AccessControl;

/**
 * Class TranslateController
 * @package school\controllers
 */
class TranslateController extends BaseController
{
    /** {@inheritDoc} */
    public function behaviors(): array
    {
        $rules = ['translations', 'translators', 'clients', 'languages', 'norms'];
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
        ];
    }
	
    /**
     *  @return mixed
     */
    public function actionTranslations()
    {
        $this->layout = 'main-2-column';
        $params = self::getUrlParams([
            'translate/tranlations',
            'TSS' => NULL,
            'LANG' => NULL,
            'MONTH' => date('m'),
            'YEAR' => date('Y'),
        ]);
        
        return $this->render('translations', [
            'languages' => Translationlang::getLanguageListSimple(),
            'years' => Tool::getYearsSimple(),
            'translations'=> Translation::getTranslationList($params),
            'urlParams' => $params
        ]);
    }
    
    /**
     *  @return mixed
     */
    public function actionTranslators()
    {
        $this->layout = 'main-2-column';
        $params = self::getUrlParams([
            'translate/tranlators',
            'TSS' => NULL,
            'LANG' => NULL,
            'NOTAR' => NULL
        ]);
        
        return $this->render('translators', [
            'languages' => Translationlang::getLanguageListSimple(),
            'translatorLanguages' => Langtranslator::getTranslatorLanguageList(),
            'translators'=> Translator::getTranslatorList($params),
            'urlParams' => $params
        ]);
    }

    /**
     *  @return mixed
     */
    public function actionClients()
    {
        $this->layout = 'main-2-column';
        $params = self::getUrlParams([
            'translate/clients',
            'TSS' => NULL,
            'LANG' => NULL
        ]);

        return $this->render('clients', [
            'clients'=> Translationclient::getClientList($params),
            'urlParams' => $params
        ]);
    }

    /**
     *  @return mixed
     */
    public function actionLanguages()
    {
        $this->layout = 'main-2-column';
        $params = self::getUrlParams([
            'translate/languages',
            'TSS' => NULL
        ]);
        
        return $this->render('languages', [
			'languages'=> Translationlang::getLanguageList($params),
            'urlParams' => $params
        ]);
    }

    /**
     * @return mixed
     */
    public function actionNorms()
    {
        $this->layout = 'main-2-column';
        $params = self::getUrlParams([
            'translate/norms',
            'TSS' => NULL
        ]);
        
        return $this->render('norms', [
			'norms'=> Translationnorm::getNormList($params),
            'urlParams' => $params
        ]);
    }

    /**
     * Метод заполняет массив значениями из GET запроса
     * @param array $urlParams
     * @return array
     */
    protected static function getUrlParams(array $urlParams): array
    {
        $params = $urlParams;
        foreach($urlParams as $key => $value) {
            if(Yii::$app->request->get($key) != 'all' && Yii::$app->request->get($key) != '') {
                $params[$key] = Yii::$app->request->get($key);
            }
        }
        return $params;
    }
}