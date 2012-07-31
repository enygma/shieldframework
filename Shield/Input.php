<?php

namespace Shield;

class Input extends Base
{
    /**
     * Internal variable for $_GET values
     * @var array
     */
    private $get     = array();

    /**
     * Internal variable for $_POST values
     * @var array
     */
    private $post    = array();

    /**
     * Internal variable for $_REQUEST values
     * @var array
     */
    private $request = array();

    /**
     * Internal variable for $_FILES values
     * @var array
     */
    private $files   = array();

    /**
     * Internal variable for $_SERVER values
     * @var array
     */
    private $server   = array();

    /**
     * Internal variable for $_SESSION values
     * @var array
     */
    private $session  = array();

    /**
     * Intiitalize the object and extract the superglobals
     * 
     * @param object $di DI container
     * 
     * @return null
     */
    public function __construct(Di $di)
    {
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->request = $_REQUEST;
        $this->files   = $_FILES;
        $this->server  = $_SERVER;
        $this->session = $_SESSION;

        unset($_GET, $_POST, $_REQUEST, $_FILES, $_SERVER);

        parent::__construct($di);
    }

    /**
     * Run the filters associated with the property name
     * 
     * @param string $name  Property name
     * @param string $value Property value
     * 
     * @return mixed Either null if the value is (false|null) or the actual value
     */
    private function filterInput($name, $value)
    {
        // look for its filter(s)
        $filter = $this->di->get('Filter');

        if ($filter == null) {
            throw new \Exception('No filter object defined!');
        }

        $val = $filter->filter($name, $value);
        return ($val !== false && $val !== null) ? $val : null;
    }

    /**
     * Set a value into the input type
     * 
     * @param string $type  Variable type
     * @param string $name  Variable name
     * @param mixed  $value Valiable value
     * 
     * @return object Shield\Input instance
     */
    public function set($type, $name, $value)
    {
        $type = strtolower($type);

        if (isset($this->$type)) {
            $this->$type[$name] = $value;

            //sessions are special
            if ($type == 'session') {
                $_SESSION[$name] = $value;
            }
        }
        return $this;
    }

    /**
     * Get all if the RAW values of the given type (GET, POST, etc.)
     * 
     * @param string $type Global type
     * 
     * @return mixed Either the array of values or NULL
     */
    public function getAll($type)
    {
        $type = '_'.strtolower($type);
        return (isset($this->$type)) ? $this->$type : null;
    }

    /**
     * Pull an information from the $_GET values
     * 
     * @param string  $name   Name of the parameter
     * @param boolean $escape Escape the output (false is NOT recommended)
     * 
     * @return mixed Either NULL or the value
     */
    public function get($name, $escape=true)
    {
        if (isset($this->get[$name])) {
            if ($escape === true) {
                return $this->filterInput($name, $this->get[$name]);
            } else {
                $this->throwError(
                    'You are using the raw GET value of "'.$name.'"! Use with caution!'
                );
                return $this->get[$name];
            }
        } else {
            return null;
        }
    }

    /**
     * Pull a value from the $_POST values
     * 
     * @param string $name Name of the variable
     * 
     * @return mixed Found value or NULL
     */
    public function post($name,$escape=true)
    {
        if (isset($this->post[$name])) {
            if ($escape === true) {
                return $this->filterInput($name, $this->post[$name]);
            } else {
                $this->throwError(
                    'You are using the raw POST value of "'.$name.'"! Use with caution!'
                );
                return $this->post[$name];
            }
        } else {
            return null;
        }
    }

    /**
     * Pull a value from the $_REQUEST values
     * 
     * @param string $name Name of the variable
     * 
     * @return mixed Found value or NULL
     */
    public function request($name,$escape=true)
    {
        if (isset($this->request[$name])) {
            if ($escape === true) {
                return $this->filterInput($name, $this->request[$name]);
            } else {
                $this->throwError(
                    'You are using the raw REQUEST value of "'.$name.'"! Use with caution!'
                );
                return $this->request[$name];
            }
        } else {
            return null;
        }
    }

    /**
     * Pull a value from the $_FILES values
     * @todo implement filtering
     * 
     * @param string $name Name of the variable
     * 
     * @return mixed Found value or NULL
     */
    public function files($name)
    {
        return (isset($this->files[$name])) ? $this->files[$name] : null;
    }

    /**
     * Pull a value from the $_SERVER values
     * 
     * @param string $name Name of the variable
     * 
     * @return mixed Found value or NULL
     */
    public function server($name,$escape=true)
    {
        if (isset($this->server[$name])) {
            if ($escape === true) {
                return $this->filterInput($name, $this->server[$name]);
            } else {
                $this->throwError(
                    'You are using the raw SERVER value of "'.$name.'"! Use with caution!'
                );
                return $this->server[$name];
            }
        } else {
            return null;
        }
    }

    /**
     * Pull a value from the $_SESSION values
     * 
     * @param string $name Name of the variable
     * 
     * @return mixed Found value or NULL
     */
    public function session($name, $escape=true)
    {
        if (isset($this->session[$name]) || isset($_SESSION[$name])) {
            $data = (isset($this->session[$name])) ? $this->session[$name] : $_SESSION[$name];

            if ($escape === true) {
                return $this->filterInput($name, $data);
            } else {
                $this->throwError(
                    'You are using the raw SESSION value of "'.$name.'"! Use with caution!'
                );
                return $this->session[$name];
            }
        } else {
            return null;
        }
    }
}
