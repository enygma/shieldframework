<?php

namespace Shield;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    private $_template = null;
    private $_di       = null;
    private $_config   = null;
    
    public function setUp()
    {
        $this->_di = new Di();
        $this->_config = new Config($this->_di);
        $this->_template = new Template($this->_config);

        $this->_di->register(new View($this->_config, $this->_template));
    }
    public function tearDown()
    {
        $this->_di = null;
        $this->_template = null;
        $this->_config = null;
    }

    /**
     * Test that a template with a replacement is correctly rendered
     * 
     * @return null
     */
    public function testTemplateReplace()
    {
        $templateString = 'testing [this]';
        $this->_template->this = 'foo';
        $this->assertEquals(
            $this->_template->render($templateString),
            'testing foo'
        );
    }

    /**
     * Test that a value can be correctly on the template object
     * 
     * @return null
     */
    public function testGetSetValue()
    {
        $this->_template->testing = 'foo';
        $this->assertEquals($this->_template->testing,'foo');
    }

}
