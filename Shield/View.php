<?php

namespace Shield;

class View extends Base
{
    /**
     * Container for the values to replace in the array
     * @var array
     */
    private $values = array();

    /**
     * View directory path
     * @var string
     */
    private $_viewDir  = null;

    /**
     * Template object instance
     * @var object
     */
    public $template = null;

    /**
     * Init the object and create a Template instance
     * 
     * @param object $di DI container
     */
    public function __construct($di)
    {
        $this->template = new Template($di);
        $this->setViewDir();

        parent::__construct($di);
    }

    /**
     * Set the directory to look for views in
     * 
     * @param string $dir Direcotry path
     */
    public function setViewDir($dir=null)
    {
        // see if the path is valid
        $viewPath = ($dir !== null) ? $dir : __DIR__.'/../app/views';

        if (realpath($viewPath) !== false) {
            $this->_viewDir = realpath($viewPath);
        }
    }

    /**
     * Get the current path for view files
     * 
     * @return string View path
     */
    public function getViewDir()
    {
        return $this->_viewDir;
    }

    /**
     * Set a new value into the view instance
     * 
     * @param string $index  String to replace
     * @param string $value  Value for the view
     * @param bool   $escape Whether to escape the value
     * 
     * @return null
     */
    public function set($index, $value, $escape=true)
    {
        if ($escape === true) {
            // escape all values
            $value = htmlspecialchars($value, ENT_QUOTES);
        }

        $this->values[$index] = $value;
    }

    /**
     * Get a value out of the currently set View values
     * 
     * @param string $index Name of value to get
     * 
     * @return mixed Found value or NULL
     */
    public function get($index)
    {
        return (isset($this->values[$index])) ? $this->values[$index] : null;
    }

    /**
     * Get the complete list of View values
     * 
     * @return array List of values (array)
     */
    private function getValues()
    {
        return $this->values;
    }

    /**
     * Render the view, do the substitution too
     * 
     * @param string $content View contents
     * 
     * @return string $content Formatted content
     */
    public function render($content)
    {
        foreach ($this->getValues() as $index => $value) {
            $content = str_replace('['.$index.']', $value, $content);
        }
        return $content;
    }
}
