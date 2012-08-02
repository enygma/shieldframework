<?php

namespace Shield;

class Session extends Base
{
    /**
     * Path to save the sessions to
     * @var string
     */
    private $savePathRoot  = '/tmp';

    /**
     * Save path of the saved path
     * @var string
     */
    private $savePath      = '';

    /**
     * Salt for hashing the session data
     * @var string
     */
    private $_key          = 't3st!ng';

    /**
     * Init the object, set up the session config handling
     * 
     * @return null
     */
    public function __construct(Di $di)
    {
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );

        $sessionKey = $di->get('Config')->get('session_key');
        if ($sessionKey !== null) {
            $this->_key = $sessionKey;
        }
        $sessionPath = $di->get('Config')->get('session_path');
        if ($sessionPath !== null) {
        	$this->savePathRoot = $sessionPath;
        }

        parent::__construct($di);
    }

    /**
     * Write to the session
     * 
     * @param integer $id   Session ID
     * @param mixed   $data Data to write to the log
     * 
     * @return null
     */
    public function write($id, $data)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $path    = $this->savePathRoot.'/shield_'.$id;
        $data    = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_key, $data, MCRYPT_MODE_CBC, $iv);

        file_put_contents($path, $data);
    }

    /**
     * Set the key for the session encryption to use (default is set)
     * 
     * @param string $key Key string
     * 
     * @return null
     */
    public function setKey($key)
    {
        $this->_key = $key;
    }

    /**
     * Read in the session
     * 
     * @param string $id Session ID
     * 
     * @return null
     */
    public function read($id)
    {
        $path = $this->savePathRoot.'/shield_'.$id;
        $data = null;

        if (is_file($path)) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
            $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $data    = file_get_contents($path);
            $data    = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->_key, $data, MCRYPT_MODE_CBC, $iv);
        }

        return $data;
    }

    /**
     * Close the session
     * 
     * @return boolean Default return (true)
     */
    public function close()
    {
        return true;
    }

    /**
     * Perform garbage collection on the session
     * 
     * @param int $maxlifetime Lifetime in seconds
     * 
     * @return null
     */
    public function gc($maxlifetime)
    {
        $path = $this->savePathRoot.'/shield_*';

        foreach (glob($path) as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Open the session
     * 
     * @param string $savePath  Path to save the session file locally
     * @param string $sessionId Session ID
     * 
     * @return null
     */
    public function open($savePath, $sessionId)
    {
        // open session
    }

    /**
     * Destroy the session
     * 
     * @param string $id Session ID
     * 
     * @return null
     */
    public function destroy($id)
    {
        $path = $this->savePathRoot.'/shield_'.$id;
        if (is_file($path)) {
            unlink($path);
        }
        return true;
    }

    /**
     * Refresh the session with a new ID
     * 
     * @return null
     */
    public function refresh()
    {
        $sess = $this->_di->get('Input')->getAll('session');
        $id = session_regenerate_id(true);
        session_destroy();
        session_start();
        $_SESSION = $sess;
    }

}
