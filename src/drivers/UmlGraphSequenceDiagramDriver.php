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
      objects already seen
    */
    protected $objects = array();
    /**
    sequence number of the last message
    */
    protected $sequenceNumber = 0;
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
     $sequenceNumber = 1;
    	      $this->addToInit( ".PS\n" .
			     'copy "/usr/local/lib/sequence.pic";');
	$this->addToInit("
# These are all the defaults 
#Variable Name  Default Value   Operation
boxht    =0.3; #       Object box height
boxwid   =0.75; #       Object box width
awid     =1.0; # Active lifeline width
spacing  =0.25;  #Spacing between messages
movewid  =0.75; #Spacing between objects
dashwid  =0.05; #Interval for dashed lines
maxpswid =11;   #Maximum width of picture
maxpsht  =11;   #Maximum height of picture
	 ");

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

		$this->registerObjectIfNew($fromObj);
		$this->registerObjectIfNew($destObj);
#		$this->addToMessageSequences('create_message('.$fromObj.','.$destObj.',"eh");'); 
		$this->addToMessageSequences('message('.$fromObj.','.$destObj.',"'.$this->sequenceNumber." ".$method.'");'); // can use $name instead of $method
		$this->addToMessageSequences('step();');
		$this->sequenceNumber++;
        } else {
//		print "                   SKIPPED caller=$caller; class=$destClass; method=$method obj=$destObj\n";
	}


    }

    /**
     * @return void
     */
    public function endFunction() {
	$this->commentToGraph("endFunction");
//    	$this->addToMessageSequences("return_message();");
	$this->addToMessageSequences("\n");
	$this->closeObjects();
    }

    /** 
    * Close the objects currently held open
    */
    protected function closeObjects() {
    	foreach ($this->objects as $object => $dummy) {
	    print "Closing $object\n";
	    $this->addToClosedown("inactive(".$object.");");
	    $this->addToClosedown("complete(".$object.");");
	}
	$this->objects = array();
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

    protected function addNode($class) {

	$obj = $this->objForClass($class);	
	return $this->registerObjectIfNew($obj);
    }

    protected function registerObjectIfNew($object) {
        
        if ($object == '') {
	   return;
	}
    	print "REGISTERING $object\n";
    	if (! $this->objects[$object]) {
	   $this->objects[$object] = 1;

	   $this->registerObject($object);
	}
    }   

    protected function registerObject ($object) {
    
	if (	
	   ($object == '') || (strpos($object, 'Obj') === false)
	   ) {
	   print "Trying to register non-object :".$object;
	   exit ("DIE");
	}

	$this->addToObjectDefinitions('object('
                   . $object.''
                   . ','
                   . '":'.$object.'"'
                   . ');'
		 );
        $this->addToObjectDefinitions('step();');
	$this->addToObjectDefinitions('active('.$object.');');
        $this->addToObjectDefinitions('step();');
        return true;
    }

    protected function debug($section, $string) {
        print "|$section | ".$string."\n";
    }

    protected function addToInit($string) {
        $this->graphInit .= $string."\n";
	$this->debug('init', $string );
    }
    

    protected function addToObjectDefinitions($string) {
        $this->graphDefinitions .= $string."\n";
	$this->debug('def', $string );
    }
   

    protected function addToMessageSequences($string) {
        $this->graphSequence .= $string."\n";
	$this->debug('seq', $string );
    }
    

    protected function addToClosedown($string) {
        $this->graphClosedown .= $string."\n";
	$this->debug('closedown', $string );
    }
    

    protected function commentToGraph($string) {
        $this->addToMessageSequences('# '.$string);
    }   

    /**
     * @return string
     */
    public function __toString() {
	$this->addToClosedown("\n.PE"); // SMELL: move

        return $this->graphInit . 
	       $this->graphDefinitions .
	       $this->graphSequence .
	       $this->graphClosedown;
    }

}
?>
