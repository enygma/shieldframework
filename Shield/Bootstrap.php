<?php

namespace Shield;

class Bootstrap extends Base
{

    public function __construct(&$di)
    {
        parent::__construct($di);

        // look for "_init" methods in this class and execute them
        $class = new \ReflectionClass(get_class($this));
        $methods = $class->getMethods();

        foreach ($methods as $method) {
            if (strstr($method->name, '_init') !== false) {
                $name = $method->name;
                $this->$name($di);
            }
        }
    }

    private function _initEnvConfig(Di &$di)
    {
        error_reporting(-1);
        ini_set('display_errors', 1);
        ini_set('session.save_handler', 'files');

        $contentType = $di->get('Config')->get('view.content-type');
        $contentType = ($contentType == null) ? 'text/html' : $contentType;

        $charset = $di->get('Config')->get('view.charset');
        $charset = ($charset == null) ? 'utf-8' : $charset;

        // render with the UTF-8 charset
        header('Content-Type: '.$contentType.'; charset='.$charset);
    }

    private function _initObjects(Di &$di)
    {
        // set up the custom encrypted session handler
        $session = new Session($di);
        $di->register($session);
        session_start();

        // see if we need to lock our session
        $sessionLock = $di->get('Config')->get('session.lock');
        if ($sessionLock == true) {
            $session->lock();
        }

        // grab our input & filter
        $di->register(new Filter($di));
        $input  = new Input($di);

        session_set_cookie_params(3600, '/', $input->server('HTTP_HOST'), 1, true);
        $di->register($input);

        $env = new Env($di);
        $env->check();
    }    
}

?>