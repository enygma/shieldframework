<?php

namespace Shield;

class Shield
{
    /**
     * Routing options container (paths & closures)
     * @var array
     */
    private $_routes  = array();

    /**
     * Logging object (Shield\Log)
     * @var object
     */
    private $_log     = null;

    /**
     * Error reporting level
     * @var string
     */
    private $_errorLevel = '-1';

    /**
     * Dependency Injection container
     * @var object
     */
    public $di        = null;

    /**
     * View object instance (Shield\View)
     * @var object
     */
    public $view      = null;

    /**
     * Init the object and its dependencies:
     * 
     *     Filter, Input, Session, Config
     *     View, Log, Di (DI container)
     * 
     *     Also register the custom session handler (encrypted)
     *
     * @return null
     */
    public function __construct()
    {
        // force all error messages
        error_reporting(-1);
        ini_set('display_errors', 1);

        spl_autoload_register(array($this,'_load'));

        // make our DI container
        $this->di = new Di();

        // set up the custom encrypted session handler
        ini_set('session.save_handler', 'files');
        $session = new Session($this->di);
        session_start();
        $this->di->register($session);

        // grab our input & filter
        $input  = new Input($this->di);
        $filter = new Filter($this->di);

        $env = new Env($this->di);
        $env->check();

        // set up the view and logger objects
        $this->view = new View($this->di);
        $this->_log = new Log($this->di);

        $config = new Config($this->di);
        $config->load();

        $this->di->register(
            array(
                $input,$filter,
                $config,$this->view,$this->_log
            )
        );
    }

    /**
     * Handle unknown property calls, looks into the DI
     *     container to see if it exists (by lowercase class name)
     * 
     * @param string $name Name of property called
     * 
     * @return mixed Either the object from DI or null
     */
    public function __get($name)
    {
        // it's not a property, let's check in the DI container
        $className = ucwords(strtolower($name));
        $obj = $this->di->get($className);

        if ($obj == null) {
            $this->_throwError('Property could not be found!');
        }

        return $obj;
    }

    /**
     * Handle unknown method calls (get() or post() - request methods)
     * 
     * @param string $func Function name
     * @param mixed  $args Arguments list
     * 
     * @return null
     */
    public function __call($func,$args)
    {
        $func = strtolower($func);
        $path = strtolower($args[0]);

        if (isset($args[1])) {
            $this->_routes[$func][$path] = $args[1];
            $this->di->Log->log('SETTING PATH ['.strtoupper($func).']: '.$path);    
        } else {
            $this->_throwError('No path to set for : '.strtoupper($func));
            $this->di->Log->log('NO PATH TO SET ['.strtoupper($func).']: '.$path);    
        }
    }

    /**
     * PSR-0 Compliant Autoloader
     * 
     * @param string $className Name of class to load (namespaced)
     * 
     * @return null
     */
    private function _load($className)
    {
        $path = __DIR__.'/'.str_replace('Shield\\', '/', $className).'.php';
        if (is_file($path)) {
            include_once $path;
        } else {
            $this->_throwError('Could not load class: '.$className);
        }
    }

    /**
     * Execute the request handling!
     * 
     * @return null
     */
    public function run()
    {
        $requestMethod = $this->di->get('Input')->server('REQUEST_METHOD');
        $queryString   = $this->di->get('Input')->server('QUERY_STRING');
        $requestUri    = $this->di->get('Input')->server('REQUEST_URI');

        // try and match our route and request type
        $uri    = strtolower(str_replace('?'.$queryString, '', $requestUri));
        $method = strtolower($requestMethod);

        if (isset($this->_routes[$method][$uri])) {

            // route match!
            $this->di->get('Log')->log('ROUTE MATCH ['.strtoupper($method).']: '.$uri);
            $routeClosure = $this->_routes[$method][$uri]();

            echo $this->view->render($routeClosure);
        } else {
            $this->di->get('Log')->log('NO ROUTE MATCH ['.strtoupper($method).']: '.$uri);
            $this->_throwError('No route match for "'.$uri.'"');
        }
    }

    /**
     * Throw a user error (NOTICE) with a given message
     * 
     * @param string $msg   Message
     * @param const  $level Error level (from E_USER_* set)
     * 
     * @return null
     */
    protected function _throwError($msg,$level=E_USER_WARNING)
    {
        trigger_error($msg, $level);
    }
}
