<?php

namespace Shield;

class Log extends Base
{
    /**
     * Path to the log file
     * @var string
     */
    private $_logPath = null;

    public function setLogPath($path)
    {
        $path = realpath($path);
        if (!is_writeable($path)) {
            $this->_throwError('Cannot set log path - not writeable!');
        } else {
            $this->_logPath = $path;
        }
    }

    /**
     * Get the current logging path
     * 
     * @return string
     */
    public function getLogPath()
    {
        return $this->_logPath;
    }

    /**
     * Make the default log path
     * 
     * @return null
     */
    public function makeLogPath($logPath=null)
    {
        $logPath = ($logPath !== null) ? $logPath : $this->_logPath;

        // check to see if we can write to it
        if (is_writable($logPath)) {
            mkdir($logPath);
            return true;
        } else {
            $this->_throwError('Cannot create logs/ directory');
            return false;
        }
    }

    /**
     * Init the object and set up the config file path
     * 
     * @param object $di DI container
     * 
     * @return null
     */
    public function __construct($di)
    {
        // check config for a path or set a default logging path
        $logPath = $di->get('Config')->get('log_path');

        if ($logPath !== null && is_dir(realpath($logPath)) && is_writable($logPath)) {
            $this->setLogPath(realpath($logPath));
        } else {
            $logPath  = __DIR__.'/../app/../logs';
            $realpath = realpath($logPath);

            if ($realpath === false) {
                // directory may not exist, try to create
                if ($this->makeLogPath($logPath) === true) {
                    $this->setLogPath($logPath);
                } else {
                    // all else fails, write to /tmp
                    $this->setLogPath('/tmp');
                }
            } else {
                $this->setLogPath($realpath);
            }
        }
    }

    /**
     * Log the message to the data source
     * 
     * @param string $msg Message to write
     * 
     * @return null;
     */
    public function log($msg,$level='info')
    {
        $logFile = $this->getLogPath().'/log-'.date('Ymd').'.log';

        if (is_writeable($this->getLogPath())) {
            $fp = fopen($logFile,'a+');
            if($fp) {
                $msg = '['.date('m.d.Y H:i:s').'] ['.strtoupper($level).'] '.$msg;
                fwrite($fp,$msg."\n");
                fclose($fp);
            }
        } else {
            $this->_throwError('Cannot write to logs/ directory');
        }
    }

}
