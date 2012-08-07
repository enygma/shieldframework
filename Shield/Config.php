<?php

namespace Shield;

class Config extends Base
{
    /**
     * Configuration options container
     * @var array
     */
    private $config = array('general'=>array());

    /**
     * Configuration file name (default)
     * @var string
     */
    private $configFile = 'config.php';

    /**
     * Load the configuration into the container (from a file)
     * 
     * @param string $path Path to config file (an array returned)
     * 
     * @throws \Exception If no config file or it's not a .php file
     * @return null
     */
    public function load($path=null)
    {
        if ($path == null) {
            $path = './'.$this->configFile;
        }
        $path = realpath($path);
        if (file_exists($path) && !is_readable($path)) {
            throw new \Exception('Cannot access configuration file!');
        }

        if ($path !== false) {
            // be sure it's a .php file
            $info = pathinfo($path);

            if ($info['extension'] !== 'php') {
                throw new \Exception('File must be a .php file!');
            } else {
                // we're good - load it!
                $data = include $path;
                $this->setConfig($data);
            }
        }   
    }

    /**
     * Set the values into the configuration container
     * 
     * @param array $config Array of configuration options
     */
    public function setConfig($config,$context='general')
    {
        $this->config[$context] = $config;
        return $this;
    }

    /**
     * Set the filename to load config from
     * 
     * @param string $fileName Name of file
     * 
     * @return null
     */
    public function setConfigFile($fileName)
    {
        $this->configFile = $fileName;
        return $this;
    }

    /**
     * Get the current value for the config filename
     * 
     * @return null
     */
    public function getConfigFile()
    {
        return $this->configFile;
    }

    /**
     * Get the full set of config options
     * 
     * @return array Config container (options)
     */
    public function getConfig($context=null)
    {
        if ($context !== null && isset($this->config[$context])) {
            return $this->config[$context];
        } else {
            return $this->config;
        }
    }

    /**
     * Get a specific configuration option
     * 
     * @param string $name Name of option
     * 
     * @return mixed Either a string value or an array
     */
    public function get($name,$context='general')
    {
        if (strstr($name, '.') !== false) {
            // an array, split it and try to find it
            $parts   = explode('.',$name);
            $current = $this->config[$context];

            foreach ($parts as $p) {
                if (!isset($current[$p])) { return null; }
                $current = $current[$p];
            }
            return $current;
        } else {
            // just a string
            return (isset($this->config[$context][$name])) 
                ? $this->config[$context][$name] : null;
        }
    }

    /**
     * Set a configuration opton
     * 
     * @param string $name  Name of option (will overwrite)
     * @param mixed  $value Value to assign
     * 
     * @return object Shield\Config
     */
    public function set($name,$value,$context='general')
    {
        $this->config[$context][$name] = $value;
        return $this;
    }

    /**
     * Use the given config and make updates to the context's settings
     * 
     * @param array  $config  Array of configuration settings
     * @param string $context Context for the config options
     * 
     * @return null
     */
    public function update($config,$context='general')
    {
        foreach ($config as $option => $value) {
            if (strstr($option, '.') !== false) {
                $opt = explode('.',$option);

            } else {
                $opt = array($option);
            }
            $this->recurseConfig($opt,$value,$this->config[$context]);
        }
    }

    /**
     * Recurse through the configuration to apply the new values
     * 
     * @param array $opt    New config option path
     * @param mixed $value  New value to set
     * @param array $config Current configuration options
     * 
     * @return null
     */
    private function recurseConfig($opt,$value,&$config)
    {
        $first = array_shift($opt);
        if (isset($config[$first])) {
            if (count($opt) == 0) {
                $config[$first] = $value;
            } else  {
                $this->recurseConfig($opt,$value,$config[$first]);
            }
        }

    }
}
