<?php
/**
 * File containing the ezcReflectionProperty class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionProperty class using PHPDoc comments to provide
 * type information
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionProperty extends ReflectionProperty {
    /**
    * @var ezcReflectionDocParser
    */
    protected $docParser = null;

    /**
    * @param mixed $class
    * @param string $name
    */
    public function __construct($class, $name) {
        parent::__construct($class, $name);

        if (method_exists($this, 'getDocComment')) {
            $this->docParser = ezcReflectionApi::getDocParserInstance();
        	$this->docParser->parse($this->getDocComment());
        }
    }

    /**
    * @return ezcReflectionType
    */
    public function getType() {
        if ($this->docParser == null) {
            return 'unknown(ReflectionProperty::getDocComment introduced at'.
                   ' first in PHP5.1)';
        }

        $vars = $this->docParser->getVarTags();
        if (isset($vars[0])) {
            return ezcReflectionApi::getTypeByName($vars[0]->getType());
        }
        else {
            return null;
        }
    }

    /**
    * @return ezcReflectionClassType
    */
    public function getDeclaringClass() {
        $class = parent::getDeclaringClass();
        return new ezcReflectionClassType($class->getName());
    }
}
?>
