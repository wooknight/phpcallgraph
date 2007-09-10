<?php
/**
 * File containing the ezcReflectionDocParser interface.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Defines an interface for documentation parsers.
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 * @author Falko Menge <mail@falko-menge.de>
 */
interface ezcReflectionDocParser {

    /**
     * Initialize parsing of the given documentation fragment.
     * Results can be retrieved after completion by the provided getters.
     * 
     * @param string $docComment
     */
    public function parse($docComment);

    /**
     * Return all found tags with the given name.
     *  
     * @param string $name
     * @return ezcReflectionDocTag[]
     */
    public function getTagsByName($name);

    /**
     * Retrieve all found tags
     * 
     * @return ezcReflectionDocTag[]
     */
    public function getTags();

    /**
     * Retrieve all param tags
     * 
     * @return ezcReflectionDocTagParam[]
     */
    public function getParamTags();

    /**
     * Retrieve all var tags
     *  
     * @return ezcReflectionDocTagVar[]
     */
    public function getVarTags();

    /**
     * Retrieve all return tags
     * 
     * @return ezcReflectionDocTagReturn[]
     */
    public function getReturnTags();

    /**
    * Checks whether a tag was used in the parsed documentation fragment
    * 
    * @param string $with name of used tag
    * @return boolean
    */
    public function isTagged($with);

    /**
     * Returns short description 
     * @return string
     */
    public function getShortDescription();

    /**
     * Returns long description 
     * @return string
     */
    public function getLongDescription();
}
?>