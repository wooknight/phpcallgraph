<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionFunctionTest extends ezcTestCase
{
    public function testGetTags() {
        $func = new ezcReflectionFunction('m1');
        $tags = $func->getTags();

        $expectedTags = array('webmethod', 'author', 'param', 'param', 'param', 'return');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);


        $func = new ezcReflectionFunction('m2');
        $tags = $func->getTags();
        $expectedTags = array('param', 'author');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);
    }

    public function testIsTagged() {
        $func = new ezcReflectionFunction('m1');
        self::assertFalse($func->isTagged('licence'));

        $func = new ezcReflectionFunction('m1');
        self::assertTrue($func->isTagged('webmethod'));
    }

    public function testGetLongDescription() {
        $func = new ezcReflectionFunction('m1');
        $desc = $func->getLongDescription();

        $expected = '';
        self::assertEquals($expected, $desc);

        $func = new ezcReflectionFunction('m2');
        $desc = $func->getLongDescription();

        $expected = '';
        self::assertEquals($expected, $desc);

        $func = new ezcReflectionFunction('m3');
        $desc = $func->getLongDescription();

        $expected = '';
        self::assertEquals($expected, $desc);

        $func = new ezcReflectionFunction('m4');
        $desc = $func->getLongDescription();

        $expected =  "This function is used to set up the DOM-Tree and to make the important\n".
                     "nodes accessible by assigning global variables to them. Furthermore,\n".
                     "depending on the used \"USE\", diferent namespaces are added to the\n".
                     "definition element.\n".
                     "Important: the nodes are not appended now, because the messages are not\n".
                     "created yet. That's why they are appended after the messages are created.";
        self::assertEquals($expected, $desc);
    }

    public function testGetShortDescription() {
        $func = new ezcReflectionFunction('m1');
        $desc = $func->getShortDescription();
        $expected = 'To check whether a tag was used';
        self::assertEquals($expected, $desc);

        $func = new ezcReflectionFunction('m2');
        $desc = $func->getShortDescription();
        $expected = '';
        self::assertEquals($expected, $desc);

        $func = new ezcReflectionFunction('m3');
        $desc = $func->getShortDescription();
        $expected = '';
        self::assertEquals($expected, $desc);

        $func = new ezcReflectionFunction('m4');
        $desc = $func->getShortDescription();
        $expected = 'Enter description here...';
        self::assertEquals($expected, $desc);
    }

    public function testIsWebmethod() {
        $func = new ezcReflectionFunction('m1');
        self::assertTrue($func->isWebmethod());

        $func = new ezcReflectionFunction('m2');
        self::assertFalse($func->isWebmethod());
    }

    public function testGetReturnDescription() {
        $func = new ezcReflectionFunction('m1');
        $desc = $func->getReturnDescription();
        self::assertEquals('Hello World', $desc);

        $func = new ezcReflectionFunction('m4');
        $desc = $func->getReturnDescription();
        self::assertEquals('', $desc);
    }

    public function testGetReturnType() {
        $func = new ezcReflectionFunction('m1');
        $type = $func->getReturnType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('string', $type->toString());

        $func = new ezcReflectionFunction('m4');
        self::assertNull($func->getReturnType());
    }

    public function testGetParameters() {
        $func = new ezcReflectionFunction('m1');
        $params = $func->getParameters();

        $expected = array('test', 'test2', 'test3');
        ReflectionTestHelper::expectedParams($expected, $params, $this);

        $func = new ezcReflectionFunction('m3');
        $params = $func->getParameters();
        self::assertTrue(count($params) == 0);
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionFunctionTest" );
    }
}
?>
