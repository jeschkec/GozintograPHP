<?php
//  expand include path to lib/ directory
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__) . PATH_SEPARATOR .  'src/');

/**
 *  GozintograPHP is the main library and does all the work
 */
require_once('lib/Gozintogra.php');

class GozintograTest extends PHPUnit_Framework_TestCase
{
    public function testStripSingleQuotes()
    {
        $unstripped = "'stripped'";
        $stripped   = 'stripped';

        $this->assertEquals($stripped, GozintograPHP::stripQuotes($unstripped));
    }

    public function testStripDoubleQuotes()
    {
        $unstripped = '"stripped"';
        $stripped   = 'stripped';

        $this->assertEquals($stripped, GozintograPHP::stripQuotes($unstripped));
    }
}
