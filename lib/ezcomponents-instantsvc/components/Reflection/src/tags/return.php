<?php
/**
 * File containing the ezcReflectionDocTagReturn class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Represents a return doc tag in the php source code comment. 
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionDocTagReturn extends ezcReflectionDocTag {
    
	/**
    * @param string[] $line array of words
    */
    public function __construct($line) {
        $this->tagName = $line[0];

        if (isset($line[1])) {
            $this->params[0] = ezcReflectionTypeMapper::getInstance()->getType($line[1]);
        }
        if (isset($line[2])) {
            $this->desc = $line[2];
        }
        if (isset($line[3])) {
            $this->desc .= ' '.$line[3];
        }
    }

    /**
    * @return string
    */
    public function getType() {
        return $this->params[0];
    }
}
?>
