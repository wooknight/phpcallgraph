<?php
/**
 * File containing the ezcReflectionDocTagWebService class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Represents a webservice doc tag in the php source code comment. 
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionDocTagWebService extends ezcReflectionDocTag {

    /**
    * @param string[] $line array of words
    */
    public function __construct($line) {
    	//$line[0] should be webservice, proof it?
        $this->tagName = $line[0];
    }
}
?>
