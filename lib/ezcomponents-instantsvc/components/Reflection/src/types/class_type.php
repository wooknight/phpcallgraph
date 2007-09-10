<?php
/**
 * File containing the ezcReflectionClassType class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Representation for all class types
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 * @author Falko Menge <mail@falko-menge.de>
 */
class ezcReflectionClassType extends ezcReflectionClass implements ezcReflectionType {

    /**
     * @return ezcReflectionType
     */
    public function getArrayType() {
        return null;
    }

    /**
     * @return ezcReflectionType
     */
    function getMapIndexType() {
        return null;
    }

    /**
     * @return ezcReflectionType
     */
    function getMapValueType() {
        return null;
    }

    /**
     * @return boolean
     */
    function isArray() {
        return false;
    }

    /**
     * @return boolean
     */
    function isClass() {
        return true;
    }

    /**
     * @return boolean
     */
    function isPrimitive() {
        return false;
    }

    /**
     * @return boolean
     */
    function isMap() {
        return false;
    }

    /**
     * @return string
     */
    function toString() {
        return $this->getName();
    }


    /**
     * @return boolean
     */
    function isStandardType() {
        return false;
    }

    /**
     * Returns XML Schema name of the complexType for the class
     *
     * The `this namespace' (tns) prefix is comonly used to refer to the
     * current XML Schema document.
     *
     * @param boolean $usePrefix augments common prefix `tns:' to the name
     * @return string
     */
    function getXmlName($usePrefix = true) {
        if ($usePrefix) {
            $prefix = 'tns:';
        } else {
            $prefix = '';
        }
        return $prefix . $this->getName();
    }

    /**
     * Returns an <xsd:complexType/>
     * @param DOMDocument $dom
     * @return DOMElement
     */
    function getXmlSchema($dom, $namespaceXMLSchema = 'http://www.w3.org/2001/XMLSchema') {

        $schema = $dom->createElementNS($namespaceXMLSchema, 'xsd:complexType');
        $schema->setAttribute('name', $this->getXmlName(false));


        $parent = $this->getParentClass();
        //if we have a parent class, we will include this infos in the xsd
        if ($parent != null) {
            $complex = $dom->createElementNS($namespaceXMLSchema, 'xsd:complexContent');
            $complex->setAttribute('mixed', 'false');
            $ext = $dom->createElementNS($namespaceXMLSchema, 'xsd:extension');
            $ext->setAttribute('base', $parent->getXmlName(true));
            $complex->appendChild($ext);
            $schema->appendChild($complex);
            $root = $ext;
        }
        else {
            $root = $schema;
        }

        $seq = $dom->createElementNS($namespaceXMLSchema, 'xsd:sequence');
        $root->appendChild($seq);
        $props = $this->getProperties();
        foreach ($props as $property) {
            $type = $property->getType();
            if ($type != null and !$type->isMap()) {
                $elm = $dom->createElementNS($namespaceXMLSchema, 'xsd:element');
                $elm->setAttribute('minOccurs', '0');
                $elm->setAttribute('maxOccurs', '1');
                $elm->setAttribute('nillable', 'true');

                $elm->setAttribute('name', $property->getName());
                $elm->setAttribute('type', $type->getXmlName(true));
            	$seq->appendChild($elm);
        	}
        }
        return $schema;
    }

    /**
        <xs:schema xmlns:tns="http://tele-task.de/model/" xmlns:ttm="http://tele-task.de/model/" elementFormDefault="qualified" targetNamespace="http://tele-task.de/model/" xmlns:xs="http://www.w3.org/2001/XMLSchema">
           <xs:complexType name="Item" />

           <xs:complexType name="Lecture">
              <xs:complexContent mixed="false">
                 <xs:extension base="tns:Item">
                    <xs:sequence>
                       <xs:element minOccurs="1" maxOccurs="1" name="id" type="xs:int" />
                       <xs:element minOccurs="0" maxOccurs="1" name="name" type="xs:string" />
                       <xs:element minOccurs="0" maxOccurs="1" name="duration" type="xs:int" />
                       <xs:element minOccurs="0" maxOccurs="1" name="namehtml" type="xs:string" />
                       <xs:element minOccurs="0" maxOccurs="1" name="streamurldsl" type="xs:string" />
                       <xs:element minOccurs="0" maxOccurs="1" name="abstract" type="xs:string" />
                       <xs:element minOccurs="0" maxOccurs="1" name="languagesId" type="xs:int" nillable="true" />
                       <xs:element minOccurs="0" maxOccurs="1" name="logo" type="xs:int" nillable="true" />
                       <xs:element minOccurs="0" maxOccurs="1" name="time" type="xs:int" nillable="true" />
                       <xs:element minOccurs="0" maxOccurs="1" name="sortdate" type="xs:string" />
                    </xs:sequence>
                 </xs:extension>
              </xs:complexContent>
           </xs:complexType>
        </xs:schema>

     */
}
?>