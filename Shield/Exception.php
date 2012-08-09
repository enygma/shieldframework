<?php

/**
 * Set of custom exceptions for Shield
 */

namespace Shield;

class ShieldException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $di  = new Di();
        $config = new Config($di);
        $di->register($config);
        $log = new Log($config);

        $log->log($message);

        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Converting the exception into a string to use for logging
     * 
     * @return string Compiled exception string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class RoutingException extends ShieldException
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        // nothing to see, move along
        parent::__construct($message, $code, $previous);
    }
}

?>