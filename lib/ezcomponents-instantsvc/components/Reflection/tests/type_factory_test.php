<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionTypeFactoryTest extends ezcTestCase
{
    /**
     * Test with primitive types
     */
    public function testGetTypePrimitive() {
        $ezcReflectionPrimitiveTypes = array('integer', 'int', 'INT', 'float', 'double',
                                'string', 'bool', 'boolean');
        $factory = new ezcReflectionTypeFactoryImpl();
        foreach ($ezcReflectionPrimitiveTypes as $prim) {
        	$type = $factory->getType($prim);
        	self::assertType('ezcReflectionType', $type);
            self::assertType('ezcReflectionPrimitiveType', $type);
        }
    }

    /**
     * Test with array types
     */
    public function testGetTypeArray() {
        $arrays = array('array<int, string>', 'array<string, ReflectionClass>',
                        'array<ReflectionClass, float>');
        $factory = new ezcReflectionTypeFactoryImpl();
        foreach ($arrays as $arr) {
            $type = $factory->getType($arr);
            self::assertType('ezcReflectionType', $type);
            self::assertType('ezcReflectionArrayType', $type);
        }
    }

    /**
     * Test with class types
     */
    public function testGetTypeClass() {
        $classes = array('ReflectionClass', 'NoneExistingClassFooBarr', 'ezcTestClass');
        $factory = new ezcReflectionTypeFactoryImpl();
        foreach ($classes as $class) {
        	$type = $factory->getType($class);
        	self::assertType('ezcReflectionType', $type);
            self::assertType('ezcReflectionClassType', $type);
        }
    }


    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionTypeFactoryTest" );
    }
}
?>
