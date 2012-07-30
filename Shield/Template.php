<?php

namespace Shield;

class Template extends Base
{
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
        $this->_properties[$name] = $value;
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
        // first see if what we've been given is a file
        $templateFile = $this->di->get('View')->getViewDir().
            '/'.$template.'.php';

        if (is_file($templateFile)) {
            extract($this->_properties);
            ob_start();
            include_once $templateFile;
            return ob_get_clean();

        } else {
            // it's just a string! fall back on str_replace
            foreach ($this->_properties as $name => $value) {
                $template = str_replace('['.$name.']',$value,$template);
            }
            return $template;
        }
    }
}
