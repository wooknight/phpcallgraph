<?php
/**
 * File containing the ezcReflectionDocTagRestIn class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Represents a restin doc tag in the php source code comment. 
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionDocTagRestIn extends ezcReflectionDocTag {

    /**
     * @var string
     */
    private $serializerClass;

    /**
    * @param string[] $line Array of words
    */
    public function __construct($line) {
        $this->tagName = $line[0];
        if (isset($line[1])) {
            $this->serializerClass = $line[1];
        }
    }

    /**
     * @return string
     */
    public function getSerializer() {
        return $this->serializerClass;
    }
}
?>