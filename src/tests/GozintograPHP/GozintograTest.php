<?php
/**
 *  GozintograPHP is the main library and does all the work
 */

namespace GozintograPHP;

class ApplicationTest extends \PHPUnit_Framework_TestCase
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
