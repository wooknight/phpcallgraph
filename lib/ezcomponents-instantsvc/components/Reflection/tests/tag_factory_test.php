<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionDocTagFactoryTest extends ezcTestCase
{
    public function testCreateTag() {
        $param  = ezcReflectionDocTagFactory::createTag('param', array('param', 'string', 'param'));

        self::assertType('ezcReflectionDocTagParam', $param);

        $var    = ezcReflectionDocTagFactory::createTag('var', array('var', 'string'));
        self::assertType('ezcReflectionDocTagVar', $var);

        $return = ezcReflectionDocTagFactory::createTag('return', array('return', 'string', 'hello', 'world'));
        self::assertType('ezcReflectionDocTagReturn', $return);
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionDocTagFactoryTest" );
    }
}
?>
