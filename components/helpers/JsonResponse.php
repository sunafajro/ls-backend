<?php

namespace app\components\helpers;

use Yii;
use yii\base\Model;
use yii\web\Response;

class JsonResponse extends Model
{
    const OK                    = 200;
    CONST OBJECT_NOT_FOUND      = 404;
    CONST METHOD_NOT_ALLOWED    = 405;
    const INTERNAL_SERVER_ERROR = 500;

    public static function ok(bool $status = true, string $message = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = self::OK;

        return [
            'code'   => self::OK,
            'status' => $status,
            'text'   => $message,
        ];
    }

    public static function methodNotAllowed(string $message = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = self::METHOD_NOT_ALLOWED;

        return [
            'code'   => self::METHOD_NOT_ALLOWED,
            'status' => false,
            'text'   => $message ?: Yii::t('app', 'Method not allowed!')
        ];
    }

    public static function objectNotFound(string $message = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = self::OBJECT_NOT_FOUND;

        return [
            'code'   => self::OBJECT_NOT_FOUND,
            'status' => false,
            'text'   => $message ?: Yii::t('app', 'Object not found!')
        ];
    }

    public static function internalServerError(string $message = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = self::INTERNAL_SERVER_ERROR;

        return [
            'code'   => self::INTERNAL_SERVER_ERROR,
            'status' => false,
            'text'   => $message ?: Yii::t('app', 'Internal server error.')
        ];
    }
}