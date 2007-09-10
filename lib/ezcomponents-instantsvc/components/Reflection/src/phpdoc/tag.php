<?php
/**
 * File containing the ezcReflectionDocTag class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Represents a doc tag in the php source code comment.
 * 
 * This class is used as standard implementation for representing
 * annotations. It is only used if no specialized tag class could be
 * found deriving from this class.
 * 
 * The comment line is tokenized by at spaces and if no further structure is recognized,
 * tokens are available at getParams. 
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionDocTag {
    /**
    * @var string
    */
    protected $tagName;

    /**
    * @var string[]
    */
    protected $params;

    /**
    * @var string
    */
    protected $desc;


    /**
    * @param string[] $line Array of words
    */
    public function __construct($line) {
        $this->tagName = $line[0];

        if (count($line) == 4) {
            $this->params[] = $line[1];
            $this->params[] = $line[2];
            $this->desc = $line[3];
        }
        elseif (count($line) == 3) {
            $this->params[] = $line[1];
            $this->desc = $line[2];
        }
        elseif (count($line) == 2) {
            $this->params[] = $line[1];
        }
        else {
            $this->params = $line;
        }
    }

    /**
    * @return string
    */
    public function getDescription() {
        return $this->desc;
    }

    /**
    * @param string $line
    */
    public function addDescriptionLine($line) {
        $this->desc .= "\n".$line;
    }

    /**
    * @return string
    */
    public function getName() {
        return $this->tagName;
    }

    /**
    * @return string[]
    */
    public function getParams() {
        return $this->params;
    }
}
?>
