<?php

namespace Shield;

class View extends Base
{
    /**
     * Container for the values to replace in the array
     * @var array
     */
    private $_values = array();

    /**
     * Set a new value into the view instance
     * 
     * @param string $index String to replace
     * @param string $value Value for the view
     * 
     * @return null
     */
    public function set($index,$value,$escape=true)
    {
        if ($escape == true) {
            // escape all values
            $value = htmlspecialchars($value);
        }

        $this->_values[$index] = $value;
    }

    /**
     * Get the complete list of View values
     * 
     * @return array List of values (array)
     */
    private function _getValues()
    {
        return $this->_values;
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
        foreach ($this->_getValues() as $index => $value) {
            $content = str_replace('['.$index.']',$value,$content);
        }
        return $content;
    }
}

?>