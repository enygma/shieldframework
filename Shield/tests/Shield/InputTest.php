<?php

namespace Shield;

class InputTest extends \PHPUnit_Framework_TestCase
{
    private $_input = null;
    private $_di    = null;
    
    public function setUp()
    {
        global $_SESSION;
        global $_GET;
        global $_POST;
        global $_REQUEST;
        global $_FILES;
        global $_SERVER;

        $this->_di    = new Di();
        $this->_input = new Input($this->_di);
    }
    public function tearDown()
    {
        $this->_di    = null;
        $this->_input = null;
    }

    /**
     * Test that, when a Input object has a Filter object set on it
     *     that the get() call correctly filters (in this case, an email address)
     * 
     * @return null
     */
    public function testValidInputIsFiltered()
    {
        global $_GET;
        global $_POST;
        global $_REQUEST;
        global $_FILES;
        global $_SERVER;

        $validEmail = 'woo@test.com';

        // create and register a Filter instance
        $filter = new Filter($this->_di);
        $filter->add('testVal','email');
        $this->_di->register($filter);

        $input = new Input($this->_di);
        $input->set('get','testVal',$validEmail);

        $result = $input->get('testVal');
        $this->assertEquals($result,$validEmail);
    }

    /**
     * Test using a closure as a filter
     * 
     * @return null
     */
    public function testFilterAsClosure()
    {
        global $_GET;
        global $_POST;
        global $_REQUEST;
        global $_FILES;
        global $_SERVER;

        $validEmail = 'woo@test.com';

        // create and register a Filter instance
        $filter = new Filter($this->_di);
        $filter->add('testVal', function($value){
            return 'returned: '.$value;
        });
        $this->_di->register($filter);

        $input = new Input($this->_di);
        $input->set('get','testVal',$validEmail);

        $result = $input->get('testVal');

        $this->assertEquals('returned: '.$validEmail,$result);
    }
}