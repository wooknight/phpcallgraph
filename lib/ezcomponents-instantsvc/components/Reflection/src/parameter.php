<?php
/**
 * File containing the ezcReflectionParameter class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionParameter class using PHPDoc comments to provide
 * type information
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
 class ezcReflectionParameter extends ReflectionParameter {
    /**
    * @var ezcReflectionType
    */
    protected $type;

    /**
    * @var ReflectionParameter
    */
    protected $parameter = null;

    /**
    * @param mixed $mixed Type info or $function for parent class
    * @param mixed $parameter ReflectionParameter or $parameter for parent class
    * @param string $type Type information from param tag
    */
    public function __construct($mixed, $parameter) {
        if ($parameter instanceof ReflectionParameter) {
            $this->parameter = $parameter;
            $this->type = ezcReflectionApi::getTypeByName($mixed);
        }
        else {
            parent::__construct($mixed, $parameter);
        }

    }

    /**
    * @return ezcReflectionType
    */
    public function getType() {
        return $this->type;
    }

    /**
    * @return bool
    */
    public function allowsNull() {
        if ($this->parameter != null) {
            return $this->parameter->allowsNull();
        }
        else {
            return parent::allowsNull();
        }
    }

    /**
    * @return bool
    */
    public function isOptional() {
        if ($this->parameter != null) {
            return $this->parameter->isOptional();
        }
        else {
            return parent::isOptional();
        }
    }

    /**
    * @return bool
    */
    public function isPassedByReference() {
        if ($this->parameter != null) {
            return $this->parameter->isPassedByReference();
        }
        else {
            return parent::isPassedByReference();
        }
    }

    /**
    * @return bool
    */
    public function isDefaultValueAvailable() {
        if ($this->parameter != null) {
            return $this->parameter->isDefaultValueAvailable();
        }
        else {
            return parent::isDefaultValueAvailable();
        }
    }

    /**
    * @return string
    */
    public function getName() {
        if ($this->parameter != null) {
            return $this->parameter->getName();
        }
        else {
            return parent::getName();
        }
    }

    /**
    * @return mixed
    */
    public function getDefaultValue() {
        if ($this->parameter != null) {
            return $this->parameter->getDefaultValue();
        }
        else {
            return parent::getDefaultValue();
        }
    }

    /**
    * Returns reflection object identified by php type hinting
    * @return ezcReflectionClassType
    */
    public function getClass() {
        if ($this->type && $this->type->isClass()) {
            return $this->type;
        }
        return null;
    }

    /**
    * @return ezcReflectionFunction
    */
    public function getDeclaringFunction() {
        if ($this->parameter != null) {
            $func = $this->parameter->getDeclaringFunction();
        }
        else {
            $func = parent::getDeclaringFunction();
        }
        if (!empty($func)) {
            return new ezcReflectionFunction($func->getName());
        }
        else {
            return null;
        }
	}

    /**
    * @return ezcReflectionClassType
    */
    function getDeclaringClass() {
        if ($this->parameter != null) {
            $class = $this->parameter->getDeclaringClass();
        }
        else {
            $class = parent::getDeclaringClass();
        }

		if (!empty($class)) {
		    return new ezcReflectionClassType($class->getName());
		}
		else {
		    return null;
		}
    }
}
?>
