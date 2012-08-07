<?php

namespace Shield;

class Shield
{
    /**
     * Routing options container (paths & closures)
     * @var array
     */
    private $routes  = array();

    /**
     * Logging object (Shield\Log)
     * @var object
     */
    private $log     = null;

    /**
     * Error reporting level
     * @var string
     */
    private $errorLevel = '-1';
    
    /**
     * Error level constants
     * @var array
     */
    private $errorConstants = array(
        1       => 'Error',
        2       => 'Warning',
        4       => 'Parse error',
        8       => 'Notice',
        16      => 'Core Error',
        32      => 'Core Warning',
        256     => 'User Error',
        512     => 'User Warning',
        1024    => 'User Notice',
        2048    => 'Strict',
        4096    => 'Recoverable Error',
        8192    => 'Deprecated',
        16384   => 'User Deprecated',
        32767   => 'All'
    );

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
        spl_autoload_register(array($this, '_load'));
        set_error_handler(array($this, '_errorHandler'));

        // make our DI container
        $this->di = new Di();

        // init the config and read it in (if it exists)
        $config = new Config($this->di);
        $config->load();
        $this->di->register($config);

        $bs = new Bootstrap($this->di);

        // set up the view and logger objects
        $this->view = new View($this->di);
        $this->log  = new Log($this->di);

        $this->di->register(array($this->view, $this->log));
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
            $this->throwError('Property could not be found!');
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
    public function __call($func, $args)
    {
        $func = strtolower($func);
        $path = strtolower($args[0]);

        if (isset($args[1])) {
            $this->routes[$func][$path] = $args[1];
            $this->di->Log->log('SETTING PATH ['.strtoupper($func).']: '.$path);    
        } else {
            $this->throwError('No path to set for : '.strtoupper($func));
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
            return true;
        } else {
           return false;
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
        $remoteAddr    = $this->di->get('Input')->server('REMOTE_ADDR');

        // if we have the config option, see if they're allowed
        $allowedHosts = $this->di->get('Config')->get('allowed_hosts');
        if (!empty($allowedHosts)) {
            if (!in_array($remoteAddr, $allowedHosts)) {
                // not allowed, fail!
                header('HTTP/1.0 401 Not Authorized');
                return false;
            }
        }

        // try and match our route and request type
        $uri    = strtolower(str_replace('?'.$queryString, '', $requestUri));
        $method = strtolower($requestMethod);

        if (isset($this->routes[$method][$uri])) {
            $this->routeMatch($method, $uri, $uri);
            
        } else {
            $found = false;

            if (isset($this->routes[$method])) {
                // loop through our routes and see if there's a regex match
                foreach ($this->routes[$method] as $route => $handler) {
                    if (preg_match('#^'.$route.'$#', $uri, $matches) === 1 && $found == false) {
                        $found = true;
                        $this->routeMatch($method, $route, $matches);
                    }
                }

                if ($found == false) {
                    // return a 404 header
                    header('HTTP/1.0 404 Not Found');

                    $this->di->get('Log')->log('NO ROUTE MATCH ['.strtoupper($method).']: '.$uri);
                    $this->throwError('No route match for "'.$uri.'"');
                }
            }
        }
    }

    /**
     * Handle the matching route callback
     * 
     * @param string $method HTTP Method
     * @param string $uri    URI/route to match
     * 
     * @return null
     */
    private function routeMatch($method,$uri,$matches=null)
    {

        // route match!
        $this->di->get('Log')->log('ROUTE MATCH ['.strtoupper($method).']: '.$uri);
        $routeClosure = $this->routes[$method][$uri]($matches);

        echo $this->view->render($routeClosure);
    }

    /**
     * Throw a user error (NOTICE) with a given message
     * 
     * @param string $msg   Message
     * @param const  $level Error level (from E_USER_* set)
     * 
     * @return null
     */
    protected function throwError($msg, $level=E_USER_WARNING)
    {
        trigger_error($msg, $level);
    }

    public function _errorHandler($errno, $errstr, $errfile, $errline)
    {
        $errString = (array_key_exists($errno, $this->errorConstants))
            ? $this->errorConstants[$errno] : $errno;

        echo '<b>'.$errString.':</b> '.$errstr.'<br/>';

        error_log($errString.' ['.$errno.']: '.$errstr.' in '.$errfile.' on line '.$errline);
    }
}
