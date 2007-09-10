<?php
/**
 * File containing the ezcReflectionTypeFactory interface.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface definition for the type factory used by the reflection
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
interface ezcReflectionTypeFactory {

    /**
     * Creates a type object for given typeName
     * @param string $typeName
     * @return ezcReflectionType
     */
    function getType($typeName);
}

?>