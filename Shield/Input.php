<?php

namespace Shield;

class Input extends Base
{
    /**
     * Internal variable for $_GET values
     * @var array
     */
    private $_get     = array();

    /**
     * Internal variable for $_POST values
     * @var array
     */
    private $_post    = array();

    /**
     * Internal variable for $_REQUEST values
     * @var array
     */
    private $_request = array();

    /**
     * Internal variable for $_FILES values
     * @var array
     */
    private $_files   = array();

    /**
     * Internal variable for $_SERVER values
     * @var array
     */
    private $_server   = array();

    /**
     * Internal variable for $_SESSION values
     * @var array
     */
    private $_session  = array();

    /**
     * Intiitalize the object and extract the superglobals
     * 
     * @param object $di DI container
     * 
     * @return null
     */
    public function __construct($di)
    {
        $this->_get     = $_GET;
        $this->_post    = $_POST;
        $this->_request = $_REQUEST;
        $this->_files   = $_FILES;
        $this->_server  = $_SERVER;
        $this->_session = $_SESSION;

        unset($_GET,$_POST,$_REQUEST,$_FILES,$_SERVER);

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
    private function _filterInput($name,$value)
    {
        // look for its filter(s)
        $filter = $this->_di->get('Filter');

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
    public function set($type,$name,$value)
    {
        $type = '_'.strtolower($type);

        if (isset($this->$type)) {
            $this->$type[$name] = $value;

            //sessions are special
            if ($type == '_session') {
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
    public function get($name,$escape=true)
    {
        if (isset($this->_get[$name])) {
            if ($escape === true) {
                return $this->_filterInput($name, $this->_get[$name]);
            } else {
                $this->_throwError(
                    'You are using the raw GET "'.$name.'" value! Use with caution!'
                );
                return $this->_get[$name];
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
        if (isset($this->_post[$name])) {
            if ($escape === true) {
                return $this->_filterInput($name, $this->_post[$name]);
            } else {
                $this->_throwError(
                    'You are using the raw POST "'.$name.'" value! Use with caution!'
                );
                return $this->_post[$name];
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
        if (isset($this->_request[$name])) {
            if ($escape === true) {
                return $this->_filterInput($name, $this->_request[$name]);
            } else {
                $this->_throwError(
                    'You are using the raw REQUEST "'.$name.'" value! Use with caution!'
                );
                return $this->_request[$name];
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
        return (isset($this->_files[$name])) ? $this->_files[$name] : null;
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
        if (isset($this->_server[$name])) {
            if ($escape === true) {
                return $this->_filterInput($name, $this->_server[$name]);
            } else {
                $this->_throwError(
                    'You are using the raw SERVER "'.$name.'" value! Use with caution!'
                );
                return $this->_server[$name];
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
    public function session($name,$escape=true)
    {
        if (isset($this->_session[$name])) {
            if ($escape === true) {
                return $this->_filterInput($name, $this->_session[$name]);
            } else {
                $this->_throwError(
                    'You are using the raw SERVER "'.$name.'" value! Use with caution!'
                );
                return $this->_session[$name];
            }
        } else {
            return null;
        }
    }
}
