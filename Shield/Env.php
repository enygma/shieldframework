<?php

namespace Shield;

class Env extends Base
{
    /**
     * Execute the checks for various environment issues
     * 
     * @return null
     */
    public function check()
    {
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
