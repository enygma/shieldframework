<?php

namespace Shield;

class View extends Base
{
    private $templateDir = null;
    private $filter      = null;
    private $charset     = 'UTF-8';
    private $contentType = 'text/html';

    public function __construct(\Shield\Filter $filter)
    {
        $this->filter = $filter;
        $this->setViewDir();
    }

    /**
     * Template properties (values)
     * @var array
     */
    private $_properties = array();

    /**
     * Magic method to get values from the properties
     * 
     * @param string $name Name of property
     * 
     * @return null
     */
    public function __get($name)
    {
        return (isset($this->_properties[$name]))
            ? $this->_properties[$name] : null;
    }

    /**
     * Magic method to set values to the properties
     * 
     * @param string $name  Property name
     * @param mixed  $value Property value
     * 
     * @return null description
     */
    public function __set($name,$value)
    {
        $this->set($name, $value);
    }

    /**
     * Set the value(s) to the template
     * 
     * @param string $index Property name
     * @param mixed  $value Property value
     * 
     * @return object $this Current View instance
     */
    public function set($index, $value=null)
    {
        if (!is_array($index)) {
            $index = array($index => $value);
        }
        foreach ($index as $i => $value) {
            $this->_properties[$i] = $value;
        }

        return $this;
    }

    /**
     * Add a filter to a property
     * 
     * @param string $index  Property name
     * @param mixed  $filter Either a filter type or a closure
     * 
     * @return object $this View instance
     */
    public function filter($index, $filter=null)
    {
        if (!is_array($index)) {
            $index = array($index => $filter);
        }
        foreach ($index as $i => $value) {
            if (is_array($value)) {
                foreach ($value as $f) {
                    $this->filter->add($i, $f);
                }
            } else {
                $this->filter->add($i, $filter);
            }
        }
        return $this;
    }

    /**
     * Get a value from our properties
     * 
     * @param string $index Index name of property
     * 
     * @return mixed Either NULL or the property value, if found
     */
    public function get($index)
    {
        return (isset($this->_properties[$index])) ? $this->_properties[$index] : null;
    }

    /**
     * Get the current templates directory
     * 
     * @return string Full path to templates directory
     */
    public function getViewDir()
    {
        return $this->templateDir;
    }

    /**
     * Set the directory where the templates live
     * 
     * @param string $dir Directory path
     * 
     * @return null
     */
    public function setViewDir($dir=null)
    {
        // see if the path is valid
        $templatePath = ($dir !== null) ? $dir : __DIR__.'/../app/views';

        if (realpath($templatePath) !== false) {
            $this->templateDir = realpath($templatePath);
        }
        return $this;
    }

    /**
     * Get the current content type value
     * 
     * @return string Content type
     */
    public function getContentType()
    {
        $contentType = Config::get('view.content-type');
        return ($contentType !== null) ? $contentType : $this->contentType;
    }

    /**
     * Set the content type (ex. "text/html")
     * 
     * @param string $contentType Content type string
     * 
     * @return object $this Template
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Get the current character set
     * 
     * @return string Character set
     */
    public function getCharset()
    {
        $charset = Config::get('view.charset');
        return ($charset !== null) ? $charset : $this->charset;
    }

    /**
     * Set the character set
     * 
     * @param string $charset Character set (ex. "UTF-8")
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Render the template - either using a file (views/) or as a string
     *     Checks to see if the $template references a file first
     * 
     * @param string $template Either a filename (views/) or a string
     * 
     * @return string Rendered output
     */
    public function render($template)
    {
        $charset     = $this->getCharset();
        $contentType = $this->getContentType();

        header('Content-Type: '.$contentType.'; charset='.$charset);

        // first see if what we've been given is a file
        $templateFile = $this->getViewDir().'/'.$template.'.php';

        // run through our properties and filter
        foreach ($this->_properties as $index => $value) {
            $this->_properties[$index] = $this->filter->filter($index,$value);
        }

        if (is_file($templateFile)) {
            extract($this->_properties);
            ob_start();
            include_once $templateFile;
            return ob_get_clean();

        } else {
            // it's just a string! fall back on str_replace
            foreach ($this->_properties as $name => $value) {
                $template = str_replace('['.$name.']', $value, $template);
            }

            // replace any leftovers
            $template = preg_replace('#\[.*?\]#', '', $template);
            return $template;
        }
    }
}
