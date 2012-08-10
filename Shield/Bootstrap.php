<?php

namespace Shield;

class Bootstrap extends Base
{

    public function __construct()
    {
        // look for "_init" methods in this class and execute them
        $class = new \ReflectionClass(get_class($this));
        $methods = $class->getMethods();

        foreach ($methods as $method) {
            if (strstr($method->name, '_init') !== false) {
                $name = $method->name;
                $this->$name();
            }
        }
    }

    private function _initEnvConfig()
    {
        error_reporting(-1);
        ini_set('display_errors', 1);
        ini_set('session.save_handler', 'files');

        $contentType = Config::get('view.content-type');
        $contentType = ($contentType == null) ? 'text/html' : $contentType;

        $charset = Config::get('view.charset');
        $charset = ($charset == null) ? 'utf-8' : $charset;

        // render with the UTF-8 charset
        header('Content-Type: '.$contentType.'; charset='.$charset);
    }

    private function _initObjects()
    {
        // set up the custom encrypted session handler
        $session = new Session();
        //$di->register($session);
        session_start();

        // see if we need to lock our session
        $sessionLock = Config::get('session.lock');
        if ($sessionLock == true) {
            $session->lock();
        }

        // grab our input & filter
        $filter = new Filter();
        $input  = new Input($filter);

        session_set_cookie_params(3600, '/', $input->server('HTTP_HOST'), 1, true);
        //$di->register($input);

        $env = new Env($input);
        $env->check();
    }    
}

?>