<?php

namespace app\modules\api\v1\controllers;

use yii\base\Exception;
use yii\base\UserException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Default controller for the `v1` module
 */
class DefaultController extends Controller
{
    /**
     * Generic error.
     *
     * @return array
     */
    public function actionError()
    {
        if (($exception = \Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new NotFoundHttpException('Endpoint not found.');
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }

        if ($exception instanceof Exception) {
            $name = $exception->getName();
        } else {
            $name = 'Error';
        }
        if ($code) {
            $name .= " (#$code)";
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = 'An internal server error occurred.';
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'name' => $name,
            'message' => $message,
            'exception' => $exception,
        ];
    }
}
