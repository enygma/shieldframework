<?php

namespace Shield;

class Filter extends Base
{
    /**
     * Container for field filters
     * @var array
     */
    private $filters = array();

    /**
     * Config instance
     * @var object
     */
    private $config  = null;

    public function __construct()
    {
        //nothing to see...
    }

    /**
     * Add a new filter
     * 
     * @param string $name Name of the field
     * @param string $type Type of filter
     * 
     * @return null
     */
    public function add($name, $type = null)
    {
        if (!is_array($name)) {
            $name = array($name => $type);
        }

        foreach ($name as $n => $type) {
            if ($type == null) { continue; }
            
            if (isset($this->filters[$n])) {
                $this->filters[$n][] = $type;
            } else {
                $this->filters[$n] = array($type);
            }
        }
    }

    /**
     * Get the filter(s) for a certain value
     * 
     * @param string $name Field name
     * 
     * @return string $func Function name
     */
    public function get($name)
    {
        $func = array();
        if (isset($this->filters[$name])) {
            foreach ($this->filters[$name] as $filter) {
                $func[] = $filter;
            }
        }
        return $func;
    }

    /**
     * Filter the given property (name) with the given value
     * 
     * @param string $name  Name of property
     * @param mixed  $value Value to filter
     * 
     * @return mixed $value Filtered value
     */
    public function filter($name, $value)
    {
        $filters = $this->get($name);

        if (count($filters) == 0) {
            $filters = array('htmlentities');
        }
        foreach ($filters as $filter) {
            if ($filter instanceof \Closure) {
                $value = $filter($value);
            } else {
                $func = 'filter'.ucwords(strtolower($filter));
                if (method_exists($this, $func)) {
                    $value = $this->$func($value);
                }
            }
        }
        return $value;
    }

    // -----------------
    /**
     * Filter the value as if it was an email
     * 
     * @param string $value Email value
     * 
     * @return mixed Either te value if it matches or NULL
     */
    private function filterEmail($value)
    {
        $val = filter_var($value, FILTER_VALIDATE_EMAIL);
        return ($val === $value) ? $val : null;
    }

    /**
     * Strip tags from the value
     * 
     * @param string $value Value to filter
     * 
     * @return string Filtered string
     */
    private function filterStriptags($value)
    {
        return strip_tags($value);
    }

    /**
     * Apply the htmlentities method on the value
     * 
     * @param string $value Value to filter
     * 
     * @return string Filtered result
     */
    private function filterHtmlentities($value)
    {
        return htmlentities($value);
    }

    /**
     * Filter the value to see if it's an integer
     * 
     * @param string $value Value to be filtered
     * 
     * @return mixed Either the value or NULL
     */
    private function filterInteger($value)
    {
        $val = filter_var($value, FILTER_VALIDATE_INT);
        return ($val == $value) ? $val : null;
    }

    /**
     * Filter the value to see if it's a value URL
     * 
     * @param string $value Value to filter
     * 
     * @return mixed Either the value or NULL
     */
    private function filterUrl($value)
    {
        $val = filter_var($value, FILTER_VALIDATE_URL);
        return ($val == $value) ? $val : null;
    }

    /**
     * Filter the value to see if it's all lowercase
     * 
     * @param string $value Value to filter
     * 
     * @return mixed Either the value or NULL
     */
    private function filterLowercase($value)
    {
        $val = strtolower($value);
        return ($val === $value) ? $val : null;
    }
}
