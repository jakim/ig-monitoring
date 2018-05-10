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
        $exception = $this->getException();

        $name = $this->getName($exception);

        $message = $this->getMessage($exception);

        return $this->prepareResponse($name, $message, $exception);
    }

    protected function getException()
    {
        if (($exception = \Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new NotFoundHttpException('Endpoint not found.');
        }

        return $exception;
    }

    protected function getCode(Exception $exception): int
    {
        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }

        return $code;
    }

    protected function getName(Exception $exception): string
    {
        if ($exception instanceof Exception) {
            $name = $exception->getName();
        } else {
            $name = 'Error';
        }

        $code = $this->getCode($exception);

        if ($code) {
            $name .= " (#$code)";
        }

        return $name;
    }

    protected function getMessage(Exception $exception): string
    {
        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = 'An internal server error occurred.';
        }

        return $message;
    }

    protected function prepareResponse($name, $message, $exception): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'name' => $name,
            'message' => $message,
            'exception' => $exception,
        ];
    }
}
