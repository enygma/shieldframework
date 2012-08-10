<?php

namespace Shield;

class Session extends Base
{
    /**
     * Path to save the sessions to
     * @var string
     */
    private $savePathRoot = '/tmp';

    /**
     * Save path of the saved path
     * @var string
     */
    private $savePath = '';

    /**
     * Salt for hashing the session data
     * @var string
     */
    private $key = '282edfcf5073666f3a7ceaa5e748cf8128bd53359b6d8269ba2450404face0ac';

    private $lock = false;

    /**
     * Init the object, set up the session config handling
     * 
     * @return null
     */
    public function __construct()
    {
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );

        $sessionKey = Config::get('session.key');
        if ($sessionKey !== null) {
            $this->key = $sessionKey;
        }
        $sessionPath = Config::get('session.path');
        $this->savePathRoot = ($sessionPath == null)
            ? ini_get('session.save_path') : $sessionPath;

        //parent::__construct($di);
    }

    /**
     * If locking is on, check the values to ensure there's a match
     * 
     * @return boolean Lock status
     */
    public function lock()
    {
        $log = new Log();
        $log->log('Session locking in effect');

        $sip = (isset($_SESSION['sIP'])) ? $_SESSION['sIP'] : null;
        $sua = (isset($_SESSION['sUA'])) ? $_SESSION['sUA'] : null;

        // if they're null, set them
        if ($sip == null && $sua == null) {
            $_SESSION['sIP'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['sUA'] = $_SERVER['HTTP_USER_AGENT'];
        } elseif ($sip !== null && $sua !== null) {
            // see if we have a match, if not refresh()
            if ($sip !== $_SERVER['REMOTE_ADDR'] || $sua !== $_SERVER['HTTP_USER_AGENT']) {
                $log->log('SECURITY ALERT: Session lock override attempt!');
                $this->refresh();
                return false;
            }
        }
        return true;
    }

    /**
     * Encrypt the given data
     * 
     * @param mixed $data Session data to encrypt
     * 
     * @return mixed $data Encrypted data
     */
    private function encrypt($data)
    {
        $ivSize  = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv      = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $keySize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $key     = substr(sha1($this->key), 0, $keySize);

        // add in our IV and base64 encode the data
        $data    = base64_encode(
            $iv.mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_CBC, $iv
            )
        );
        return $data;
    }

    /**
     * Decrypt the given session data
     * 
     * @param mixed $data Data to decrypt
     * 
     * @return $data Decrypted data
     */
    private function decrypt($data)
    {
        $data    = base64_decode($data, true);

        $ivSize  = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $keySize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $key     = substr(sha1($this->key), 0, $keySize);

        $iv   = substr($data, 0, $ivSize);
        $data = substr($data, $ivSize);

        $data = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_CBC, $iv
        );

        return $data;
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
        $path = $this->savePathRoot.'/shield_'.$id;
        $data = $this->encrypt($data);
        
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
        $this->key = $key;
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
            // get the data and extract the IV
            $data = file_get_contents($path);
            $data = $this->decrypt($data);
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
     * DO NOT repopulate the session information
     * 
     * @return null
     */
    public function refresh()
    {
        $sid = session_id();
        if (!empty($sid)) {
            error_log('refreshing session!');

            $id = session_regenerate_id(true);
            session_destroy();
            session_start();
        }
    }

}
