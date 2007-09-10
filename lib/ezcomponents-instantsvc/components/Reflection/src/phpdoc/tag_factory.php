<?php
/**
 * File containing the ezcReflectionDocTagFactory class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Creates a ezcReflectionDocTag object be the given doctag
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionDocTagFactory {

    /**
    * @param string $type
    * @param string[] $line array of words
    * @return ezcReflectionDocTag
    */
    static public function createTag($type, $line) {
        $tagClassName = 'ezcReflectionDocTag'.$type;
        $tag = null;
        if (class_exists($tagClassName)) {
            $tag = new $tagClassName($line);
        }
        else {
            $tag = new ezcReflectionDocTag($line);
        }
        return $tag;
    }
}
?>
