<?php

namespace Shield;

class Log extends Base
{
    /**
     * Path to the log file
     * @var string
     */
    private $_logPath = null;

    /**
     * Init the object and set up the config file path
     * 
     * @param object $di DI container
     * 
     * @return null
     */
    public function __construct($di)
    {
        // nothing to see, move along
        $this->_logPath = __DIR__.'/../app/logs';
        if (!is_dir($this->_logPath)) {
            mkdir($this->_logPath);
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
        $logFile = $this->_logPath.'/log-'.date('Ymd').'.log';
        $fp = fopen($logFile,'a+');
        if($fp) {
            $msg = '['.date('m.d.Y H:i:s').'] ['.strtoupper($level).'] '.$msg;
            fwrite($fp,$msg."\n");
            fclose($fp);
        }
    }

}

?>