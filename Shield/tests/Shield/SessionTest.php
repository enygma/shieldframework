<?php

namespace Shield;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    private $_di      = null;
    private $_session = null;
    private $_input   = null;
    private $_config  = null;
    private $_filter  = null;

    public static function setUpBeforeClass()
    {
        ini_set('session.save_handler', 'files');
    }
    
    public function setUp()
    {
        global $_GET;
        global $_POST;
        global $_REQUEST;
        global $_SERVER;
        global $_FILES;
        global $_SESSION;

        $this->_di      = new Di();
        $this->_filter  = new Filter($this->_config);
        $this->_input   = new Input($this->_filter);
        //$this->_config  = new Config();

        // $this->_di->register($this->_filter);

        // $this->_di->register(array(
        //     $this->_input, $this->_config
        // ));

        $this->_session = new Session();

        //$this->_di->register($this->_session);

        session_start();
    }
    public function tearDown()
    {
        $this->_di = null;
    }

    public function testSetSessionValue()
    {
        global $_GET;
        global $_POST;
        global $_REQUEST;
        global $_SERVER;
        global $_FILES;
        global $_SESSION;

        $testVal = '12345';

        $this->_input->set('session','testval',$testVal);
        $result = $this->_input->session('testval');

        $this->assertEquals($testVal,$result);
    }
}