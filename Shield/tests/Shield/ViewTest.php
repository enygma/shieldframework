<?php

namespace Shield;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    private $_view = null;
    private $_di   = null;
    
    public function setUp()
    {
        $this->_di   = new Di();
        $this->_view = new View($this->_di);
    }
    public function tearDown()
    {
        $this->_di   = null;
        $this->_view = null;
    }

    /**
     * Test that a value is correctly set into the View
     * 
     * @return null
     */
    public function testViewValueSet()
    {
        $value = 'testing123';
        $this->_view->set('test',$value);
        $this->assertEquals(
            $value,$this->_view->get('test')
        );
    }

    /**
     * Test that the templating replaces the value correctly
     * 
     * @return null
     */
    public function testTemplateReplace()
    {
        $value = 'testing123';
        $this->_view->set('test',$value);
        $output = $this->_view->render('my test: [test]');

        $this->assertEquals(
            $output,'my test: testing123'
        );
    }

}
