<?php

namespace Shield;

class Env extends Base
{
    private $input  = null;

    public function __construct(\Shield\Input $input)
    {
        $this->input  = $input;
    }

    /**
     * Execute the checks for various environment issues
     * 
     * @return null
     */
    public function check()
    {
        $this->checkHttps();
        $this->setFrameHeader();
        $this->checkRegisterGlobals();
        $this->checkMagicQuotes();
    }

    /**
     * Set a X-Frame-Options header
     * 
     * @return null
     */
    private function setFrameHeader()
    {
        header('X-Frame-Options: deny');
    }

    /**
     * Check to see if we need to move to HTTPS by default
     * 
     * @return null
     */
    private function checkHttps()
    {
        // see if we need to be on HTTPS
        $httpsCfg = Config::get('force_https');
        $httpsSet = $this->input->server('HTTPS');

        if ($httpsCfg == true && empty($httpsSet)) {
            $host    = $this->input->server('HTTP_HOST');
            $request = $this->input->server('REQUEST_URI');

            $redirect= "https://".$host.$request;
            header("Location: $redirect");
        }
    }

    /**
     * Check the register_globals setting
     * 
     * @return boolean Enabled/not enabled
     */
    private function checkRegisterGlobals()
    {
        if (ini_get('register_globals')) {
            $this->throwError('SECURITY WARNING: register_globals is enabled! '
                .'Please consider disabling.');
        } else {
            return true;
        }
    }

    /**
     * Check the magic_quotes setting
     * 
     * @return boolean Enabled/not enabled
     */
    private function checkMagicQuotes()
    {
        if (get_magic_quotes_gpc() || get_magic_quotes_runtime()) {
            $this->throwError('SECURITY WARNING: magic_quotes is enabled! '
                .'Please consider disabling');
        } else {
            return true;
        }
    }
}
