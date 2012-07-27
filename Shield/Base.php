<?php

namespace Shield;

class Base
{
    /**
     * DI Container (shared)
     * 
     * @var object
     */
    protected $di = null;

    /**
     * Init the Base object, inject the DI container
     * 
     * @param object $di DI Container
     */
    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    /**
     * Throw a user error (NOTICE) with a given message
     * 
     * @param string $msg   Message
     * @param const  $level Error level (from E_USER_* set)
     * 
     * @return null
     */
    protected function throwError($msg, $level=E_USER_WARNING)
    {
        trigger_error($msg, $level);
    }
}
