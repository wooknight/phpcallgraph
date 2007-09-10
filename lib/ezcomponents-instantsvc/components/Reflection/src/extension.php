<?php
/**
 * File containing the ezcReflectionExtension class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionExtension class using PHPDoc comments to provide
 * type information
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionExtension extends ReflectionExtension {

    /**
    * @param string $name
    */
    public function __construct($name) {
        parent::__construct($name);
    }

    /**
    * @return ezcReflectionFunction[]
    */
    public function getFunctions() {
        $functs = parent::getFunctions();
        $result = array();
        foreach ($functs as $func) {
        	$function = new ezcReflectionFunction($func->getName());
        	$result[] = $function;
        }
        return $result;
    }

    /**
     * @return ezcReflectionClassType[]
     */
    public function getClasses() {
        $classes = parent::getClasses();
        $result = array();
        foreach ($classes as $class) {
        	$extClass = new ezcReflectionClassType($class->getName());
        	$result[] = $extClass;
        }
        return $result;
    }
}
?>