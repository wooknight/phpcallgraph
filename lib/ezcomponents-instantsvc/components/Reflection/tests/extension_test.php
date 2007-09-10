<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionExtensionTest extends ezcTestCase
{
    public function testGetFunctions() {
        $ext = new ezcReflectionExtension('Spl');
        $functs = $ext->getFunctions();
        foreach ($functs as $func) {
            self::assertType('ezcReflectionFunction', $func);
        }

        $ext = new ezcReflectionExtension('Reflection');
        $functs = $ext->getFunctions();
        self::assertEquals(0, count($functs));
    }

    public function testGetClasses() {
        $ext = new ezcReflectionExtension('Reflection');
        $classes = $ext->getClasses();

        foreach ($classes as $class) {
            self::assertType('ezcReflectionClassType', $class);
        }
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionExtensionTest" );
    }
}
?>
