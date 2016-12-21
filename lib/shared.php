<?php

/**
 * check if environment is development and set error reporting
 * 
 * 
 */
function setReporting()
{
    if (DEVELOPMENT_ENVIRONMENT == true)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
    }
    else
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'Off');
        ini_set('log_errors', 'On');
        ini_set('error_log', ROOT . DS . 'tmp' . DS . 'logs' . DS . 'error.log');
    }
}

/**
 * extends stripshlashes
 *
 * @param string $value            
 */
function deepStripSlashes($value)
{
    $value = is_array($value) ? array_map('deepStripSlashes', $value) : stripcslashes($value);
    return $value;
}

/**
 * check for Magic Quotes and remove them
 */
function removeMagicQuotes()
{
    if (get_magic_quotes_gpc()) {
        $_GET = deepStripSlashes($_GET);
        $_POST = deepStripSlashes($_POST);
        $_COOKIE = deepStripSlashes($_COOKIE);
    }
}

/**
 * check for registered globals and remove them
 */
function unregisterGlobals()
{
    if (ini_get('register_globals')) 
    {
        $array = [
            '_SESSION',
            '_POST',
            '_GET',
            '_COOKIE',
            '_REQUEST',
            '_SERVER',
            '_ENV',
            '_FILES'
        ];
        
        foreach ($array as $value) 
        {
            foreach ($GLOBALS[$value] as $key => $var) 
            {
                if ($var === $GLOBALS[$key])
                    unset($GLOBALS[$key]);
            }
        }
    }
}

function callProgram()
{
    global $url;
    
    $urlArray = [];
    $urlArray = explode('/', $url);
    
    $controller = $urlArray[0];
    array_shift($urlArray);
    
    $action = $urlArray[0];
    array_shift($urlArray);
    
    $query = $urlArray;
    
    $controllerName = $controller;
    $controller = ucwords($controller);
    $model = rtrim($controller, 's');
    $controller .= 'Controller';
    $dispatch = new $controller($model, $controllerName, $action);
    
    if ((int) method_exists($controller, $action))
        call_user_func_array([
            $dispatch,
            $action
        ], $query);
    else 
    {
    /**
     * generate error code
     */
    }
}

function __autoload($className)
{
    if (file_exists(ROOT . DS . 'lib' . DS . strtolower($className) . '.class.php'))
        require_once (ROOT . DS . 'lib' . DS . strtolower($className) . '.class.php');
    elseif (file_exists(ROOT . DS . 'app' . DS . 'controllers' . DS . strtolower($className) . '.php'))
        require_once (ROOT . DS . 'app' . DS . 'controllers' . DS . strtolower($className) . '.php');
    elseif (file_exists(ROOT . DS . 'app' . DS . 'models' . DS . strtolower($className) . '.php'))
        require_once (ROOT . DS . 'app' . DS . 'controllers' . DS . strtolower($className) . '.php');
    else 
    {
    /**
     * generate error code
     */
    }
}

setReporting();
removeMagicQuotes();
unregisterGlobals();
callProgram();
