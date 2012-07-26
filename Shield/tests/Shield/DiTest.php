<?php

namespace Shield;

class DiTest extends \PHPUnit_Framework_TestCase
{
    private $_di = null;
    
    public function setUp()
    {
        $this->_di = new Di();
    }
    public function tearDown()
    {
        $this->_di = null;
    }

    /**
     * Test that an object is correctly injected into the DI container
     * 
     * @return null
     */
    public function testObjectRegister()
    {
        $obj = new \stdClass();
        $obj->testing = 'foo';

        $this->_di->register($obj);
        $ret = $this->_di->get('stdClass');

        $this->assertTrue(isset($ret->testing) && $ret->testing == 'foo');
    }

}

?>