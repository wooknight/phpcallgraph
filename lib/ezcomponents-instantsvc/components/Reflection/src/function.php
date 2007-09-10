<?php
/**
 * File containing the ezcReflectionFunction class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionFunction class using PHPDoc comments to provide
 * type information
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionFunction extends ReflectionFunction
{
    /**
    * @var ezcReflectionDocParser
    */
    protected $docParser;

    /**
    * @param string $name
    */
    public function __construct($name) {
        parent::__construct($name);
        $this->docParser = ezcReflectionApi::getDocParserInstance();
        $this->docParser->parse($this->getDocComment());
    }

    /**
    * @return ezcReflectionParameter[]
    */
    function getParameters() {
        $params = $this->docParser->getParamTags();
        $extParams = array();
        $apiParams = parent::getParameters();
        foreach ($apiParams as $param) {
            $found = false;
            foreach ($params as $tag) {
            	if ($tag->getParamName() == $param->getName()) {
            	   $extParams[] = new ezcReflectionParameter($tag->getType(),
            	                                             $param);
            	   $found = true;
            	   break;
            	}
            }
            if (!$found) {
                $extParams[] = new ezcReflectionParameter(null, $param);
            }
        }
        return $extParams;
    }

    /**
    * Returns the type defined in PHPDoc tags
    * @return ezcReflectionType
    */
    function getReturnType() {
        $re = $this->docParser->getReturnTags();
        if (count($re) == 1 and isset($re[0])) {
            return ezcReflectionApi::getTypeByName($re[0]->getType());
        }
        return null;
    }

    /**
    * Returns the description after a PHPDoc tag
    * @return string
    */
    function getReturnDescription() {
        $re = $this->docParser->getReturnTags();
        if (count($re) == 1 and isset($re[0])) {
            return $re[0]->getDescription();
        }
        return '';
    }

    /**
    * Check whether this method has a @webmethod tag
    * @return boolean
    */
    function isWebmethod() {
        return $this->docParser->isTagged("webmethod");
    }

    /**
    * @return string
    */
    public function getShortDescription() {
        return $this->docParser->getShortDescription();
    }

    /**
    * @return string
    */
    public function getLongDescription() {
        return $this->docParser->getLongDescription();
    }

    /**
    * @param string $with
    * @return boolean
    */
    public function isTagged($with) {
        return $this->docParser->isTagged($with);
    }

    /**
    * @param string $name
    * @return ezcReflectionDocTag[]
    */
    public function getTags($name = '') {
        if ($name == '') {
            return $this->docParser->getTags();
        }
        else {
            return $this->docParser->getTagsByName($name);
        }
    }
}
?>
