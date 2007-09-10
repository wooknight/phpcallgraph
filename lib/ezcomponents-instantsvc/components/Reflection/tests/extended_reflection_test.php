<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionTest extends ezcTestCase
{
    public function testGetTypeByName() {
        $string = ezcReflectionApi::getTypeByName('string');
        self::assertEquals('string', $string->toString());

        $int = ezcReflectionApi::getTypeByName('int');
        self::assertEquals('integer', $int->toString());

        $webservice = ezcReflectionApi::getTypeByName('TestWebservice');
        self::assertEquals('TestWebservice', $webservice->toString());

        $class = ezcReflectionApi::getTypeByName('ezcReflectionClass');
        self::assertEquals('ezcReflectionClass', $class->toString());

    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionTest" );
    }
}
?>
