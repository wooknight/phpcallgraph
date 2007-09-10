<?php
/**
 * File containing the ezcReflectionDocTagWebMethod class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Represents a webmethod doc tag in the php source code comment. 
 * 
 * @todo enhance tag with additional parameters, maybe information to name it
 *       in the wsdl file or what else may be usefull (have look at java and
 *       .net annotations)
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionDocTagWebMethod extends ezcReflectionDocTag {

    /**
    * @param string[] $line Array of words
    */
    public function __construct($line) {
    	//$line[0] should be webmethod, proof it?
        $this->tagName = $line[0];
    }
}
?>
