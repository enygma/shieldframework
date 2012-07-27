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
            $className = ($alias !== null) 
                ? $alias : str_replace(__NAMESPACE__.'\\', '', get_class($i));

            if (!array_key_exists($className, $this->objects)) {
                $this->objects[$className] = $i;
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
        return (array_key_exists($name, $this->objects))
            ? $this->objects[$name] : null;
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
}
