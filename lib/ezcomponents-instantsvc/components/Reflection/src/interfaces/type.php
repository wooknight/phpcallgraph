<?php
/**
 * File containing the ezcReflectionType interface.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for type objects representing a type/class
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
interface ezcReflectionType {

    /**
     * Return type of elements in an array type or null if is not an array
     * 
     * @return ezcReflectionType
     */
    function getArrayType();

    /**
     * Returns type of key used in a map
     * 
     * @return ezcReflectionType
     */
    function getMapIndexType();

    /**
     * Returns type of values used in a map
     * 
     * @return ezcReflectionType
     */
    function getMapValueType();

    /**
     * @return boolean
     */
    function isArray();

    /**
     * @return boolean
     */
    function isClass();

    /**
     * @return boolean
     */
    function isPrimitive();

    /**
     * @return boolean
     */
    function isMap();

    /**
     * Return the name of this type as string
     * 
     * @return string
     * @todo approve name, may be getName is better
     */
    function toString();

    //** Advanced infos for xml mapping ************************************
    /**
     * @return boolean
     */
    function isStandardType();

    /**
     * Returns the name to be used in a xml schema for this type
     * @return string
     */
    function getXmlName();

    /**
     * @param DOMDocument $dom
     * @return DOMElement
     */
    function getXmlSchema($dom);
}
?>