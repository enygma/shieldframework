<?php

namespace Shield;

class Base
{
    /**
     * DI Container (shared)
     * @var object
     */
    protected $_di = null;

    /**
     * Init the Base object, inject the DI container
     * 
     * @param object $di DI Container
     */
    public function __construct($di)
    {
        $this->_di = $di;
    }

    /**
     * Throw a user error (NOTICE) with a given message
     * 
     * @param string $msg   Message
     * @param const  $level Error level (from E_USER_* set)
     * 
     * @return null
     */
    protected function _throwError($msg,$level=E_USER_WARNING)
    {
        trigger_error($msg, $level);
    }
}
