<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionMethodTest extends ezcTestCase
{
    public function testGetDeclaringClass() {
        $method = new ezcReflectionMethod('TestMethods', 'm1');
        $class = $method->getDeclaringClass();
        self::assertType('ezcReflectionClassType', $class);
        self::assertEquals('TestMethods', $class->getName());
    }

    public function testIsMagic() {
        $method = new ezcReflectionMethod('TestMethods', 'm1');
        self::assertFalse($method->isMagic());

        $class = $method->getDeclaringClass();
        self::assertTrue($class->getConstructor()->isMagic());
    }

    public function testGetTags() {
        $class = new ezcReflectionClass('ezcReflectionClass');
        $method = $class->getMethod('getMethod');
        $tags = $method->getTags();
        self::assertEquals(2, count($tags));


        $method = new ezcReflectionMethod('TestMethods', 'm4');
        $tags = $method->getTags();
        $expectedTags = array('webmethod', 'author', 'param', 'param', 'param', 'return');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);

        $tags = $method->getTags('param');
        $expectedTags = array('param', 'param', 'param');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);

        $method = new ezcReflectionMethod('TestMethods', 'm1');
        $tags = $method->getTags();
        $expectedTags = array('param', 'author');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);
    }

    public function testIsTagged() {
        $method = new ezcReflectionMethod('TestMethods', 'm4');
        self::assertTrue($method->isTagged('webmethod'));
        self::assertFalse($method->isTagged('fooobaaar'));
    }

    public function testGetLongDescription() {
        $method = new ezcReflectionMethod('TestMethods', 'm3');
        $desc = $method->getLongDescription();

        $expected = "This is the long description with may be additional infos and much more lines\nof text.\n\nEmpty lines are valide to.\n\nfoo bar";
        self::assertEquals($expected, $desc);
    }

    public function testGetShortDescription() {
        $method = new ezcReflectionMethod('TestMethods', 'm3');
        $desc = $method->getShortDescription();

        $expected = "This is the short description";
        self::assertEquals($expected, $desc);
    }

    public function testIsWebmethod() {
        $method = new ezcReflectionMethod('TestMethods', 'm3');
        self::assertFalse($method->isWebmethod());
        $method = new ezcReflectionMethod('TestMethods', 'm4');
        self::assertTrue($method->isWebmethod());
    }

    public function testGetReturnDescription() {
        $method = new ezcReflectionMethod('TestMethods', 'm4');
        $desc = $method->getReturnDescription();
        self::assertEquals('Hello World', $desc);
    }

    public function testGetReturnType() {
        $method = new ezcReflectionMethod('TestMethods', 'm4');
        $type = $method->getReturnType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('string', $type->toString());
    }

    public function testGetParameters() {
        $method = new ezcReflectionMethod('ezcReflectionMethod', 'getTags');
        $params = $method->getParameters();

        $expectedParams = array('name');
        foreach ($params as $param) {
            self::assertType('ezcReflectionParameter', $param);
            self::assertContains($param->getName(), $expectedParams);

            ReflectionTestHelper::deleteFromArray($param->getName(), $expectedParams);
        }
        self::assertEquals(0, count($expectedParams));
    }

    public function testIsInherited() {
        $method = new ezcReflectionMethod('TestMethods2', 'm2');
        self::assertFalse($method->isInherited());

        //is internal has been inherited an not redefined from ReflectionFunction
        $method = new ezcReflectionMethod('ReflectionMethod', 'isInternal');
        self::assertTrue($method->isInherited());

        $method = new ezcReflectionMethod('TestMethods2', 'm3');
        self::assertTrue($method->isInherited());

        $method = new ezcReflectionMethod('TestMethods2', 'newMethod');
        self::assertFalse($method->isInherited());

        $method = new ezcReflectionMethod('ezcReflectionMethod', 'isInherited');
        self::assertFalse($method->isInherited());
    }

    public function testIsOverriden() {
        $method = new ezcReflectionMethod('TestMethods2', 'm2');
        self::assertTrue($method->isOverridden());

        $method = new ezcReflectionMethod('TestMethods2', 'newMethod');
        self::assertFalse($method->isOverridden());

        $method = new ezcReflectionMethod('TestMethods2', 'm4');
        self::assertFalse($method->isOverridden());

        $method = new ezcReflectionMethod('ezcReflectionMethod', 'isInternal');
        self::assertFalse($method->isOverridden());
    }

    public function testIsIntroduced() {
        $method = new ezcReflectionMethod('TestMethods2', 'm2');
        self::assertFalse($method->isIntroduced());

        $method = new ezcReflectionMethod('TestMethods2', 'newMethod');
        self::assertTrue($method->isIntroduced());

        $method = new ezcReflectionMethod('TestMethods2', 'm4');
        self::assertFalse($method->isIntroduced());
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionMethodTest" );
    }
}
?>
