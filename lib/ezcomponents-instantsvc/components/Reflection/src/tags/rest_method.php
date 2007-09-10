<?php
/**
 * File containing the ezcReflectionDocTagRestMethod class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Represents a restmethod doc tag in the php source code comment. 
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionDocTagRestMethod extends ezcReflectionDocTag {

    /**
     * @var string
     */
    private $httpMethod = '';

    /**
     * @var string
     */
    private $pattern = '';

    /**
    * @param string[] $line Array of words
    */
    public function __construct($line) {
    	//$line[0] should be webmethod, proof it?
        $this->tagName = $line[0];
        if (isset($line[1])) {
            $this->httpMethod = $line[1];
        }
        if (isset($line[2])) {
            $this->pattern = $line[2];
        }
    }

    /**
     * @return string
     */
    public function getHttpMethod() {
        return $this->httpMethod;
    }

    /**
     * @return string
     */
    public function getRequestPattern() {
        return $this->pattern;
    }
}
?>