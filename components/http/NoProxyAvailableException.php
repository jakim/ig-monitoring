<?php

namespace app\components\http;


use Exception;

class NoProxyAvailableException extends Exception
{
    protected $message = 'No proxy available.';
}