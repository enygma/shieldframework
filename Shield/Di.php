<?php

namespace Shield;

class Di
{
    /**
     * Objects container
     * @var array
     */
    private $_objects = array();

    /**
     * Init the object
     * 
     * @return null
     */
    public function __construct()
    {
        // nothing to see, move along
    }

    /**
     * Register a new object into the container
     * 
     * @param boolean $new Create a new instance instead of replacing (not used)
     * 
     * @return null
     */
    public function register($instance,$alias=null)
    {
        if (!is_array($instance)) {
            $instance = array($instance);
        }
        foreach ($instance as $i) {
            $className = ($alias !== null) 
                ? $alias : str_replace(__NAMESPACE__.'\\', '', get_class($i));

            if (!array_key_exists($className, $this->_objects)) {
                $this->_objects[$className] = $i;
            }
        }
    }

    /**
     * Get an object from the container
     * 
     * @param string $name Name of the object
     * 
     * @return mixed Either the object found or null
     */
    public function get($name)
    {
        return (array_key_exists($name, $this->_objects))
            ? $this->_objects[$name] : null;
    }
}

?>