<?php
/**
 * Implementation of a call graph generation strategy wich renders output as a UML Graph Sequence Diagram input file
 *
 * PHP version 5
 *
 * This file is part of PHPCallGraph.
 *
 * PHPCallGraph is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPCallGraph is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    PHPCallGraph
 * @author     Martin Cleaver <mrjc at users dot sourceforge dot net>
 * @copyright  2009 Martin Cleaver
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */

// reduce warnings because of PEAR dependency
error_reporting(E_ALL ^ E_NOTICE);

require_once 'CallgraphDriver.php';

/**
 * implementation of a call graph generation strategy which renders output as a UML Graph Sequence Diagram input file
 * a later evolution may output as the graphic, for now we do the input file.
 */
class UmlGraphSequenceDiagramDriver implements CallgraphDriver {

    /** 
      classes already seen
    */
    protected $classes = array();
    protected $outputFormat;
    protected $useColor = true;
    protected $graphInput = '';
    protected $graph;
    protected $currentCaller = '';
    protected $internalFunctions;

    /**
     * @return CallgraphDriver
     */
    public function __construct($outputFormat = 'txt', $sequenceLibrary = '/usr/local/lib/sequence.pic') {
    	print "\n\n\n\n===================================\n";
        $this->initializeNewGraph();
        $this->setSequenceLibrary($sequenceLibrary);
        $this->setOutputFormat($outputFormat);
        $functions = get_defined_functions();
        $this->internalFunctions = $functions['internal'];
    }

    public function __destruct() {
    	   print "===========Finishing: \n\n";
//	   print $this->graphInput;
    }

    /**
     * @return void
     */
    public function reset() {
        $this->initializeNewGraph();
    }

    /**
     * @return void
     */
    protected function initializeNewGraph() {
    	      $this->addToGraph( ".PS\n" .
			     'copy "/usr/local/lib/sequence.pic";');
    }

    /**
     * Sets path to sequence library
     * @param string $sequenceLibrary  Path to sequence library, either relative or absolute
     * @return void
     */
    public function setSequenceLibrary($sequenceLibrary = 'sequence.pic') {
        $this->sequenceLibrary = $sequenceLibrary;
    }

    /**
     * Sets output format
     * @param string $outputFormat 'txt' for the raw chart, or one of the output formats supported by UMLGraph
     * @return void
     */
    public function setOutputFormat($outputFormat = 'txt') {
    	if ($outputFormat != 'txt') {
	   print "WARNING: $outputFormat not yet supported\n";
	}
        $this->outputFormat = $outputFormat;
    }

    /**
     * Enables or disables the use of color
     * @param boolean $boolean True if color should be used
     * @return void
     */
    public function setUseColor($boolean = true) {
        $this->useColor = $boolean;
    }

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function startFunction($line, $file, $name) {
        print "startFunction:  $file:$line = $name\n";
	$this->commentToGraph("startFunction:  $file:$line = $name");
	$classAndMethod = $this->getClassAndMethod($name);
        $class = $classAndMethod['class'];
        $method = $classAndMethod['method'];

        $this->addNode($class);
        $this->currentCaller = $name;
    }

    protected function objForCaller ($caller) {
       $caller = $this->removeAnyParameters($caller);
       return $this->objForClass($caller);
//        $classAndMethod = $this->getClassAndMethod($caller);
//        $class = $classAndMethod['class'];
//	return $this->objForClass($class);
    }

    protected function removeAnyParameters ($string) {
      $paramsStart = strpos($string, '(');
      if ($paramsStart) {
      	 $string = substr($string, 0, $paramsStart);
      }
      return $string;
    }

    protected function removeAnyMethod($string) {
//    print "removeAnyMETHOD $string\n";
      $methodStart = strpos($string, '::');
      if ($methodStart) {
         $string = substr($string, 0, $paramsStart);
      }
//      print "ANS=$string\n";
      return $string;
    }

    protected function objForClass ($class) {
    	$class = $this->removeAnyMethod($class);
     	$class = $this->removeAnyParameters($class);
	$obj = $class;

	// This is a hack, sadly, to eliminate stray 'Obj' prefixes.
    	if (strpos($class,'Obj') === false) {
	   print "Adding Obj to $class\n";
	   $obj = 'Obj'.$class;
	} else {
	   print "$obj already contained Obj\n";
	}

    	return $obj;
    } 

    protected function registerClassIfNew($class) {
        
        if ($class == '') {
	   return;
	}
    	print "REGISTERING $class\n";
    	if (! $this->classes[$class]) {
	   $this->classes[$class] = 1;

	   $this->registerClass($class);
	}
    }   

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function addCall($line, $file, $name) {
	$caller = $this->currentCaller;
//         print "addCall from $caller:$line to $file = $class, $method\n";


	$classAndMethod = $this->getClassAndMethod($name);
	$destClass = $classAndMethod['class'];
	$method = $classAndMethod['method'];
	$method = $this->removeAnyParameters($method);

	$fromObj = $this->objForCaller($caller);
	$destObj = $this->objForClass($destClass);


        if ($destClass != 'ClassUnknown') {
		print "                   from caller=$caller:line to $file;\n";
		print "                    class=$destClass; method=$method obj=$destObj\n";
		print "$fromObj->$destObj\n";

		$this->registerClassIfNew($fromObj);
		$this->registerClassIfNew($destClass);
		$this->addToGraph('message('.$fromObj.','.$destObj.',"'.$method.'");'); // can use $name instead of $method
		$this->addToGraph('step();');
        } else {
//		print "                   SKIPPED caller=$caller; class=$destClass; method=$method obj=$destObj\n";
	}


    }

    /**
     * @return void
     */
    public function endFunction() {
    	print "endFunction\n\n";
//    	$this->addToGraph("return_message();");
	$this->addToGraph("\n");
	$this->closeObjects();
    }

    /** 
    * Close the classes currently held open
    * Likely that this should be called close Objects, and that the class list should be an object list
    */
    protected function closeObjects() {
    	foreach ($this->classes as $class => $dummy) { // smell dummy should be objects in the class.
	    print "Closing $class\n";
	    $this->addToGraph("complete(".$this->objForClass($class).")");
	}
	$this->classes = array();
    } 

    protected function getClassAndMethod($name) {
        $nameParts = explode('::', $name);
        $class = 'ClassUnknown'; // SMELL was 'default'
        $method = $name;
        if (count($nameParts) == 2) { // method call
            if (empty($nameParts[0])) {
                $class = 'ClassUnknown';
            } else {
                $class = $nameParts[0];
            }
            // obtain method name
            $method = $nameParts[1];
        }
	return array('class' => $class, 'method' => $method);

    }

    protected function notUsed() {
        if (count($nameParts) == 1) { // function call
            if (in_array($label, $this->internalFunctions)) { // call to internal function
                $class = 'internal PHP functions';
            }
        }
}		

    /**
     * @return boolean whether it was a valid add
     */

    protected function addNode($name) {

        print "addNode: $name\n";
	$classAndMethod = $this->getClassAndMethod($name);
	$class = $classAndMethod['class'];
	$method = $classAndMethod['method'];
	$this->registerClass($class);
    }

    protected function registerClass ($class) {
    
	if ($class == 'ClassUnknown' || $class == '') {
	   return false;
	}
	$obj = $this->objForClass($class);
	$this->addToGraph('object('
                   . $obj.''
                   . ','
                   . '":'.$class.'"'
                   . ');'
		 );
	$this->addToGraph('active('.$obj.');');
        $this->addToGraph('step();');
        return true;
    }

    protected function addToGraph($string) {
        $this->graphInput .= $string."\n";
	print "|| \n";
	print "||    ".$string."\n";
	print "|| \n";
    }
    

    protected function commentToGraph($string) {
        $this->graphInput .= '# '.$string."\n";
    }   

    /**
     * @return string
     */
    public function __toString() {
	$this->addToGraph("\n.PE");

        return $this->graphInput;
    }

}
?>
