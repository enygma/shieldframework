<?php

namespace Shield;

class Template extends Base
{
    private $_properties = array();

    public function __get($name)
    {
        return (isset($this->_properties[$name]))
            ? $this->_properties[$name] : null;
    }
    public function __set($name,$value)
    {
        $this->_properties[$name] = $value;
    }
    public function render($template)
    {
        // first see if what we've been given is a file
        $templateFile = $this->_di->get('View')->getViewDir().
            '/'.$template.'.php';

        error_log($templateFile);

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