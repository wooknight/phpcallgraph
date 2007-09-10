<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ReflectionTestHelper {

    /**
     * Helper method to delete a given value from an array
     *
     * @param mixed $needle
     * @param mixed $array
     */
    static public function deleteFromArray($needle, &$array) {
        foreach ($array as $key => $value) {
            if ($value == $needle) {
                unset($array[$key]);
                return;
            }
        }
    }

    /**
     * Checks if all expected tags and only these are set
     *
     * @param string[] $expectedTags
     * @param ezcReflectionDocTag[] $tags
     * @param ezcTestCase $test
     */
    static public function expectedTags($expectedTags, $tags, $test) {
        foreach ($tags as $tag) {
            $test->assertType('ezcReflectionDocTag', $tag);
            $test->assertContains($tag->getName(), $expectedTags);

            self::deleteFromArray($tag->getName(), $expectedTags);
        }
        $test->assertEquals(0, count($expectedTags));
    }


    /**
     * Checks if all expected parameters and only these are set
     *
     * @param string[] $expectedTags
     * @param ezcReflectionDocTag[] $tags
     * @param ezcTestCase $test
     */
    static public function expectedParams($expectedParams, $params, $test) {
        foreach ($params as $param) {
            $test->assertType('ezcReflectionParameter', $param);
            $test->assertContains($param->getName(), $expectedParams);

            self::deleteFromArray($param->getName(), $expectedParams);
        }
        $test->assertEquals(0, count($expectedParams));
    }

}

?>