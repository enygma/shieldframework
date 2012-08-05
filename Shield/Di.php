<?php

namespace Shield;

class Di
{
    /**
     * Objects container
     * @var array
     */
    private $objects = array();

    /**
     * Init the object
     * 
     * @return null
     */
    public function __construct()
    {
        // these are not the constructors you were looking for..
    }

    /**
     * Register a new object into the container
     * 
     * @param boolean $new Create a new instance instead of replacing (not used)
     * 
     * @return null
     */
    public function register($instance, $alias=null)
    {
        if (!is_array($instance)) {
            $instance = array($instance);
        }
        foreach ($instance as $i) {
            $this->createInstance($i, $alias);
        }
    }

    /**
     * Get an object from the container
     * 
     * @param string $name Name of the object
     * 
     * @return mixed Either the object found or null
     */
    public function get($name, $autoInit=false)
    {
        if (array_key_exists($name, $this->objects)) {
            return $this->objects[$name];
        } else {
            if ($autoInit == true) {
                $this->createInstance($name);
                return $this->objects[$name];
            }
        }
    }

    /**
     * Use the magic method to see if an object's in our set
     * 
     * @param string $name Object name
     * 
     * @return object
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    private function createInstance($instance, $alias=null)
    {
        if (!is_object($instance)) {
            // it's a string, make an object
            $instance = __NAMESPACE__.'\\'.$instance;
            $instance = new $instance($this);
        }

        $className = ($alias !== null) 
            ? $alias : str_replace(__NAMESPACE__.'\\', '', get_class($instance));

        if (!array_key_exists($className, $this->objects)) {
            $this->objects[$className] = $instance;
            return true;
        } else {
            return false;
        }
    }
}
