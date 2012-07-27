<?php

namespace Shield;

class Config extends Base
{
    /**
     * Configuration options container
     * @var array
     */
    private $config = array();

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

    /**
     * Set the values into the configuration container
     * 
     * @param array $config Array of configuration options
     */
    public function setConfig($config)
    {
        $this->config = $config;
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
        return $this->_configFile;
    }

    /**
     * Get the full set of config options
     * 
     * @return array Config container (options)
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get a specific configuration option
     * 
     * @param string $name Name of option
     * 
     * @return mixed Either a string value or an array
     */
    public function get($name)
    {
        return (isset($this->config[$name])) ? $this->config[$name] : null;
    }

    /**
     * Set a configuration opton
     * 
     * @param string $name  Name of option (will overwrite)
     * @param mixed  $value Value to assign
     * 
     * @return object Shield\Config
     */
    public function set($name,$value)
    {
        $this->config[$name] = $value;
        return $this;
    }
}
