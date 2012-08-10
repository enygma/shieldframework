<?php

namespace Shield;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    private $_template = null;
    private $_di       = null;
    private $_config   = null;
    private $_filter   = null;
    
    public function setUp()
    {
        $this->_di = new Di();
        Config::load();
        $this->_filter = new Filter($this->_config);
        $this->_template = new View($this->_filter);

        //$this->_di->register(new View($this->_template));
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
