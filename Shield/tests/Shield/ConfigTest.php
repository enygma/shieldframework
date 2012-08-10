<?php

namespace Shield;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $_config = null;
    private $_di     = null;
    
    public function setUp()
    {
        $this->_di      = new Di();
        //$this->_config  = new Config($this->_di);
    }
    public function tearDown()
    {
        $this->_di = null;
        //$this->_config = null;
    }

    /**
     * Test that setting/getting a configuration value works
     * 
     * @return null
     */
    public function testSetGetValue()
    {
        Config::set('testing','foo123');
        $this->assertEquals(
            Config::get('testing'),'foo123'
        );
    }

    /**
     * Test that the default configuration filename is set
     * 
     * @return null
     */
    public function testGetDefaultConfigFilename()
    {
        $filename = Config::getConfigFile();
        $this->assertEquals($filename,'config.php');
    }

    /**
     * Test that the configuration filename value is set correctly
     * 
     * @return null
     */
    public function testSetConfigFilename()
    {
        $originalConfig = Config::getConfigFile();
        $newFilename    = 'testconfig.php';

        Config::setConfigFile($newFilename);

        $this->assertEquals(
            $newFilename,
            Config::getConfigFile()
        );

        // reset it back to the original
        Config::setConfigFile($originalConfig);
    }

    /**
     * Test that the custom configuration can be set correctly
     * 
     * @return null
     */
    public function testSetCustomConfiguration()
    {
        $option = 'foo123';
        $config = array('test' => $option);

        Config::setConfig($config);

        $this->assertEquals(
            Config::get('test'),
            $option
        );
    }

}
