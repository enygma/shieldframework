<?php

namespace Shield;

class ShieldTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // just in case there's already a session going
        $sid = session_id();
        if ($sid) {
            session_destroy();
            session_write_close();
        }
    }
    public function tearDown()
    {
        $this->_shield = null;
    }

    /**
     * Test that an exact match route is correctly handled
     * 
     * @return null
     */
    public function testRouteMatch()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $app = new Shield();
        $app->get('/', function(){
            echo 'match /';
        });

        ob_start();
        $app->run();

        $output = ob_get_clean();
        $this->assertEquals('match /', $output);
    }

    /**
     * Test that a regex route is matched correctly
     * 
     * @return null
     */
    public function testRegexRouteMatch()
    {
        $_SERVER['REQUEST_URI'] = '/testing123';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $app = new Shield();
        $app->get('/testing[0-9]+', function(){
            echo 'match /';
        });

        ob_start();
        $app->run();

        $output = ob_get_clean();
        $this->assertEquals('match /', $output);
    }
}
