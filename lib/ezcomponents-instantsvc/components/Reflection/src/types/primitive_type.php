<?php
/**
 * File containing the ezcReflectionPrimitiveType class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Representation for all primitive types like string, integer, float
 * and boolean
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 * @author Falko Menge <mail@falko-menge.de>
 */
class ezcReflectionPrimitiveType extends ezcReflectionAbstractType {

    /**
     * @var string
     */
    private $typeName;

    /**
     * @param string $typeName
     */
    public function __construct($typeName) {
        $this->typeName = $typeName;
    }

    /**
     * @return boolean
     */
    public function isPrimitive() {
        return true;
    }

    /**
     * @return string
     */
    public function toString() {
        return $this->typeName;
    }

    /**
     * @return boolean
     */
    function isStandardType() {
        if ($this->typeName != 'mixed' and $this->typeName != 'void') {
            return true;
        }
        return false;
    }

    /**
     * Returns name of the correspondent XML Schema datatype
     *
     * The prefix `xsd' is comonly used to refer to the
     * XML Schema namespace.
     *
     * @param boolean $usePrefix augments common prefix `xsd:' to the name
     * @return string
     */
    function getXmlName($usePrefix = true) {
        if ($usePrefix) {
            $prefix = 'xsd:';
        } else {
            $prefix = '';
        }
        return $prefix . ezcReflectionTypeMapper::getInstance()->getXmlType($this->typeName);
    }

    /**
     * @param DOMDocument
     * @return DOMElement
     */
    function getXmlSchema($dom) {
        return null;
    }
}
?>
