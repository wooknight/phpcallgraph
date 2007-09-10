<?php
/**
 * File containing the ezcReflectionDocTagParam class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Represents a param doc tag in the php source code comment. 
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionDocTagParam extends ezcReflectionDocTag {

    /**
    * @param string[] $line Array of words
    */
    public function __construct($line) {
        $this->tagName = $line[0];

        if (isset($line[1])) {
            $this->params[0] = ezcReflectionTypeMapper::getInstance()->getType($line[1]);
        }
        if (isset($line[2]) and strlen($line[2])>0) {
            if ($line[2]{0} == '$') {
                $line[2] = substr($line[2], 1);
            }
            $this->params[1] = $line[2];
        }
        if (isset($line[3])) {
            $this->desc = $line[3];
        }
    }

    /**
    * @return string
    */
    public function getParamName() {
        if (isset($this->params[1])) {
            return $this->params[1];
        }
        else {
            return null;
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
