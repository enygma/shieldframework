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

    private $contentType = 'text/html';

    private $charset = 'UTF-8';

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
     * Set the Content-Type for the View response
     * 
     * @param string $type Content-Type
     */
    public function setContentType($type)
    {
        $this->contentType = $type;
    }

    /**
     * Get the current Content-Type value
     * 
     * @return string Content-Type value
     */
    public function getContentType()
    {
        $cfg = $this->di->get('Config')->get('view.content-type');
        return ($cfg !== null) ? $cfg : $this->contentType;
    }

    /**
     * Set the Character Set for the response
     * 
     * @param string $charset Character set type
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Get the current Character Set value
     * 
     * @return string Current Character Set
     */
    public function getCharset()
    {
        // see if its in the config first
        $cfg = $this->di->get('Config')->get('view.charset');
        return ($cfg !== null) ? $cfg : $this->charset;
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
        $charset     = $this->getCharset();
        $contentType = $this->getContentType();

        header('Content-Type: '.$contentType.'; charset='.$charset);

        foreach ($this->getValues() as $index => $value) {
            $content = str_replace('['.$index.']', $value, $content);
        }
        return $content;
    }
}
