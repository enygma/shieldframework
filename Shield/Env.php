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
        $this->_setFrameHeader();

        $this->_checkRegisterGlobals();
        $this->_checkMagicQuotes();
    }

    /**
     * Set a X-Frame-Options header
     * 
     * @return null
     */
    private function _setFrameHeader()
    {
        header('X-Frame-Options: deny');
    }

    /**
     * Check the register_globals setting
     * 
     * @return boolean Enabled/not enabled
     */
    private function _checkRegisterGlobals()
    {
        $reg = ini_get('register_globals');
        if ($reg !== '') {
            $this->_throwError('SECURITY WARNING: register_globals is enabled! '
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
    private function _checkMagicQuotes()
    {
        $quotes = ini_get('magic_quotes');

        if ($quotes !== '' && $quotes !== false) {
            $this->_throwError('SECURITY WARNING: magic_quotes is enabled! '
                .'Please consider disabling');
        } else {
            return true;
        }
    }
}
