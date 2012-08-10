<?php

namespace Shield;

class Config extends Base
{
    /**
     * Configuration options container
     * @var array
     */
    private static $config = array('general'=>array());

    /**
     * Configuration file name (default)
     * @var string
     */
    private static $configFile = 'config.php';

    public function __construct()
    {
        //nothing to see...
    }

    /**
     * Load the configuration into the container (from a file)
     * 
     * @param string $path Path to config file (an array returned)
     * 
     * @throws \Exception If no config file or it's not a .php file
     * @return null
     */
    public static function load($path=null)
    {
        if ($path == null) {
            $path = './'.self::$configFile;
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
                self::setConfig($data);
            }
        }   
    }

    /**
     * Set the values into the configuration container
     * 
     * @param array $config Array of configuration options
     */
    public static function setConfig($config,$context='general')
    {
        self::$config[$context] = $config;
    }

    /**
     * Set the filename to load config from
     * 
     * @param string $fileName Name of file
     * 
     * @return null
     */
    public static function setConfigFile($fileName)
    {
        self::$configFile = $fileName;
    }

    /**
     * Get the current value for the config filename
     * 
     * @return null
     */
    public static function getConfigFile()
    {
        return self::$configFile;
    }

    /**
     * Get the full set of config options
     * 
     * @return array Config container (options)
     */
    public static function getConfig($context=null)
    {        
        if ($context !== null && isset(self::$config[$context])) {
            return self::$config[$context];
        } else {
            return self::$config;
        }
    }

    /**
     * Get a specific configuration option
     * 
     * @param string $name Name of option
     * 
     * @return mixed Either a string value or an array
     */
    public static function get($name,$context='general')
    {
        if (strstr($name, '.') !== false) {
            // an array, split it and try to find it
            $parts   = explode('.',$name);
            $current = self::$config[$context];

            foreach ($parts as $p) {
                if (!isset($current[$p])) { return null; }
                $current = $current[$p];
            }
            return $current;
        } else {
            // just a string
            return (isset(self::$config[$context][$name])) 
                ? self::$config[$context][$name] : null;
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
    public static function set($name,$value,$context='general')
    {
        self::$config[$context][$name] = $value;
    }

    /**
     * Use the given config and make updates to the context's settings
     * 
     * @param array  $config  Array of configuration settings
     * @param string $context Context for the config options
     * 
     * @return null
     */
    public static function update($config,$context='general')
    {
        foreach ($config as $option => $value) {
            if (strstr($option, '.') !== false) {
                $opt = explode('.',$option);
            } else {
                $opt = array($option);
            }
            self::recurseConfig($opt,$value,self::$config[$context]);
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
    private static function recurseConfig($opt,$value,&$config)
    {
        $first = array_shift($opt);
        if (isset($config[$first])) {
            if (count($opt) == 0) {
                $config[$first] = $value;
            } else  {
                self::recurseConfig($opt,$value,$config[$first]);
            }
        }

    }
}
