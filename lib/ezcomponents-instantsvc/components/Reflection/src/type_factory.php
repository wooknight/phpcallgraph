<?php
/**
 * File containing the ezcReflectionTypeFactoryImpl class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Implements type mapping from string to ezcReflectionType
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionTypeFactoryImpl implements ezcReflectionTypeFactory {

    private $mapper;

    public function __construct() {
        $this->mapper = ezcReflectionTypeMapper::getInstance();
    }

    /**
     * Creates a type object for given type name
     * @param string $typeName
     * @return ezcReflectionType
     * @todo ArrayAccess stuff, how to handle? has to be implemented
     */
    public function getType($typeName) {
        $typeName = trim($typeName);
        //For void null is returned
        if ($typeName == null or strlen($typeName) < 1 or strtolower($typeName) == 'void') {
            return null;
        }
        //First check whether it is an primitive type
        if ($this->mapper->isPrimitive($typeName)) {
            return new ezcReflectionPrimitiveType($this->mapper->getType($typeName));
        }
        //then check whether it is an array type
        elseif ($this->mapper->isArray($typeName)) {
            return new ezcReflectionArrayType($typeName);
        }
        //else it has to be a user class
        else {
            return new ezcReflectionClassType($typeName);
        }
    }
}

?>