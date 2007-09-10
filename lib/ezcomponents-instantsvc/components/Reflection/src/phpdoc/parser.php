<?php
/**
 * File containing the ezcReflectionPhpDocParser class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Provides structured data from PHP Documentation comments
 * 
 * Parser is implemented as state based parser using a state transisiton
 * table consisting of transition rules for empty and non-empty lines.
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 * @author Falko Menge <mail@falko-menge.de>
 */
class ezcReflectionPhpDocParser implements ezcReflectionDocParser {
	
	const BEGINNING  = 10;
	const SHORT_DESC = 0;
	const LONG_DESC  = 1;
	const TAGS 		 = 2;
	
    /**
    * @var string
    */
    protected $docComment;

    /**
    * @var int STATE
    */
    protected $state = self::BEGINNING;

    /**
    * @var array<int,int>
    */
    protected $stateTable = array(
    							true => array ( // empty lines
    							  self::BEGINNING  => self::BEGINNING,
                                  self::SHORT_DESC => self::LONG_DESC,
                                  self::LONG_DESC  => self::LONG_DESC,
                                  self::TAGS       => self::TAGS),
                                  
                                false => array ( // non empty lines
    							  self::BEGINNING  => self::SHORT_DESC,
                                  self::SHORT_DESC => self::SHORT_DESC,
                                  self::LONG_DESC  => self::LONG_DESC,
                                  self::TAGS       => self::TAGS)
                                  );
    /**
    * @var ezcReflectionDocTag
    */
    protected $lastTag = null;

    /**
    * @var string
    */
    protected $shortDesc;

    /**
    * @var string
    */
    protected $longDesc;

    /**
    * @var ezcReflectionDocTag[]
    */
    protected $tags;

    public function __construct() {
        $this->tags = array();
    }

    public function parse($docComment) {
    	$this->docComment = $docComment;
    	
        $lines = explode("\n", $this->docComment);

        foreach ($lines as $line) {
            $line = trim($line);
            $line = $this->stripDocPrefix($line);

            // in some states we need to do something
            if (!empty($line)) {
                if ($line{0} == '@' or $this->state == self::TAGS) {
                    $this->parseTag($line);
                }
                else {
                    if ($this->state == self::SHORT_DESC
                        	or $this->state == self::BEGINNING) {
                        $this->shortDesc .= $line . "\n";
                    }
                    elseif ($this->state == self::LONG_DESC) {
                        $this->longDesc .= $line . "\n";
                    }
                }
            }
            else if ($this->state == self::LONG_DESC) {
                $this->longDesc .= "\n";
            }
            
            //next state
            $this->state = $this->stateTable[empty($line)][$this->state];
        }
        $this->shortDesc = trim($this->shortDesc);
        $this->longDesc = trim($this->longDesc);
    }

    /**
    * @param string $line
    * @return string
    */
    protected function stripDocPrefix($line) {
        while (strlen($line) > 0 and ($line{0} == '/' or $line{0} == '*')) {
            $line = substr($line, 1);
        }

        return trim($line);
    }

    /**
    * @param string $line
    * @return void
    */
    protected function parseTag($line) {
        if (strlen($line) > 0) {
            if ($line{0} == '@') {
                $line = substr($line, 1);
                $words = explode(' ', $line, 4);
                $tag = ezcReflectionDocTagFactory::createTag($words[0], $words);
                $this->tags[$tag->getName()][] = $tag;
                $this->lastTag = $tag;
            }
            else {
                //no leading @, it is assumed a description is multiline
                if ($this->lastTag != null) {
                    $this->lastTag->addDescriptionLine($line);
                }
            }
        }
    }

    /**
    * @param string $name
    * @return ezcReflectionDocTag[]
    */
    public function getTagsByName($name) {
        if (isset($this->tags[$name])) {
            return $this->tags[$name];
        }
        else {
            return array();
        }
    }

    /**
    * @return ezcReflectionDocTag[]
    */
    public function getTags() {
        $result = array();
        foreach ($this->tags as $tags) {
            foreach ($tags as $tag) {
                $result[] = $tag;
            }
        }
        return $result;
    }

    /**
    * @return ezcReflectionDocTagParam[]
    */
    public function getParamTags() {
        return $this->getTagsByName('param');
    }

    /**
    * @return ezcReflectionDocTagVar[]
    */
    public function getVarTags() {
        return $this->getTagsByName('var');
    }

    /**
    * @return ezcReflectionDocTagReturn[]
    */
    public function getReturnTags() {
        return $this->getTagsByName('return');
    }

    /**
    * To check whether a tag was used
    * @param string $with name of used tag
    * @return boolean
    */
    public function isTagged($with) {
        return isset($this->tags[$with]);
    }

    /**
    * @return string
    */
    public function getShortDescription() {
        return $this->shortDesc;
    }

    /**
    * @return string
    */
    public function getLongDescription() {
        return $this->longDesc;
    }
}
?>
