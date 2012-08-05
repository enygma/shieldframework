<?php

return array(
    /**
     * You can use this setting to specify a log file directory
     */
    //'log_path' => '/tmp'
    
    /**
     * Put in the IP addresses of allowed hosts here
     */
    //'allowed_hosts' => array('127.0.0.1')
    
    /**
     * Session management settings
     */
    'session' => array(
        /**
         * Turn on/off session locking. Locking binds the session to the user's
         * IP address and User-Agent combo to help prevent session fixation & guessing
         */
        'lock' => false,

        /**
        * Use this to set the sessions directory
        */
        'path' => '/tmp',

        /**
        * Use this to set the cipher key for the session to your value
        */
        'key'  => null
    ),

    /**
     * Force the site to run under HTTPS (will try to redirect if HTTP)
     */
    'force_https' => false
);
