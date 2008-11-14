<?php
/**
 * Call graph generation class.
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
 * @author     Falko Menge <fakko at users dot sourceforge dot net>
 * @copyright  2007 Falko Menge
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */

require_once 'drivers/CallgraphDriver.php';
require_once 'drivers/TextDriver.php';

/**
 * call graph generation class
 *
 * This class requires eZ Components autoloader to be configured
 * because it leverages InstantSVC CodeAnalyzer and Extended Reflection API
 */
class PHPCallGraph {

    const VERSION = '0.6.4';

    protected $internalFunctions;
    protected $internalKeywords;
    protected $constants;
    protected $showExternalCalls = true;
    protected $showInternalFunctions = false;
    protected $driver;
    protected $codeSummary;
    protected $methodLookupTable;
    protected $propertyLookupTable;

    /**
     * Methods or function to ignore
     * var string[]
     */
    protected $ignoreList = array();

    /**
     * Decides wether debug information is printed
     * @var boolean
     */
    protected $debug = false;

    /**
     * PHP file with an autoload function which will be included into the
     * sandbox of the InstantSVC CodeAnalyzer
     * @var string
     */
    protected $autoloadFile = '';

    /**
     * Sets the output driver and initializes
     * @param CallgraphDriver $driver output driver to use
     * @return PHPCallGraph
     */
    public function __construct(CallgraphDriver $driver = null) {
        if ($driver != null) {
            $this->driver = $driver;
        } else {
            $this->driver = new TextDriver();
        }
        $functions = get_defined_functions();
        $this->internalFunctions = $functions['internal'];
        // PHP Keywords from manual
        $this->internalKeywords = array(
            'array', 'declare', 'die', 'echo', 'elseif', 'empty', 'eval',
            'exit', 'for', 'foreach', 'global', 'if', 'include', 'include_once',
            'isset', 'list', 'print', 'require', 'require_once', 'return',
            'switch', 'unset', 'while', 'catch', 'or', 'and', 'xor', 'new Exception');
        $this->constants = array_keys(get_defined_constants());

        //TODO: provide setter for this
        //$this->ignoreList = array('PHPCallGraph::setDriver', 'PHPCallGraph::__construct', 'PHPCallGraph::setShowExternalCalls', 'PHPCallGraph::setShowInternalFunctions', 'PHPCallGraph::save', 'PHPCallGraph::__toString');
    }

    public function setDriver(CallgraphDriver $driver = null) {
        if ($driver != null) {
            $this->driver = $driver;
        }
    }

    /**
     * Enable or disable printing of debug information
     * @param boolean $enabled
     */
    public function setDebug($enabled = true) {
        $this->debug = $enabled;
    }

    /**
     * Sets a PHP file with an autoload function which will be included into
     * the sandbox of the InstantSVC CodeAnalyzer
     * @param string $filename Name of a PHP file with an autoload function
     * @return boolean success
     */
    public function setAutoloadFile($filename) {
        $returnValue = false;
        if (!empty($filename) and is_file($filename) and is_readable($filename)) {
            $this->autoloadFile = $filename;
            $returnValue = true;
        } else {
            //TODO: throw exception
        }
        return $returnValue;
    }

    public function setShowExternalCalls($boolean = true) {
        $this->showExternalCalls = $boolean;
    }

    public function setShowInternalFunctions($boolean = true) {
        $this->showInternalFunctions = $boolean;
    }

    public function collectFileNames(array $filesOrDirs, $recursive = false) {
        $files = array();
        foreach ($filesOrDirs as $fileOrDir) {
            if (is_file($fileOrDir)) {
                $files[] = $fileOrDir;
            } elseif (is_dir($fileOrDir)) {
                $globbed = glob("$fileOrDir/*");
                if ($recursive) {
                    $files = array_merge($files, $this->collectFileNames($globbed, true));
                } else {
                    foreach($globbed as $path) {
                        if (is_file($path)) {
                            $files[] = $path;
                        }
                    }
                }
            }
        }
        return $files;
    }

    public function parse(array $filesOrDirs, $recursive = false) {
        $files = $this->collectFileNames($filesOrDirs, $recursive);
        $ca = new iscCodeAnalyzer(null);
        $ca->setDebug($this->debug);
        $ca->setAutoloadFile($this->autoloadFile);
        $ca->inspectFiles($files);
        $this->codeSummary = $ca->getCodeSummary();
        $this->analyseCodeSummary();
    }

    public function parseFile($file) {
        $ca = new iscCodeAnalyzer(null);
        $ca->setDebug($this->debug);
        $ca->setAutoloadFile($this->autoloadFile);
        $ca->inspectFiles(array($file));
        $this->codeSummary = $ca->getCodeSummary();
        $this->analyseCodeSummary();
    }

    public function parseDir($dir = '.') {
        $ca = new iscCodeAnalyzer($dir);
        $ca->setDebug($this->debug);
        $ca->setAutoloadFile($this->autoloadFile);
        $ca->collect();
        $this->codeSummary = $ca->getCodeSummary();
        $this->analyseCodeSummary();
    }

    public function analyseCodeSummary() {
        $this->buildLookupTables();

        //TODO: analyze code in the global scope
        // currently a workarround is to manually wrap such code
        // in a dummy function called dummyFunctionForFile_filename_php()

        // analyze functions
        if (!empty($this->codeSummary['functions'])) {
            foreach ($this->codeSummary['functions'] as $functionName => $function) {
                $this->parseMethodBody(
                        '-',
                        $functionName,
                        array(),
                        array(),
                        $function['file'],
                        $function['startLine'],
                        $function['endLine']
                        );
            }
        }

        // analyze classes
        if (!empty($this->codeSummary['classes'])) {
            foreach ($this->codeSummary['classes'] as $className => $class) {
                /*
                echo $className, "\n";
                var_export($class);
                echo "\n\n";
                //*/
                if (!empty($class['methods'])) {
                    $propertyNames = array_keys($class['properties']);
                    $methodNames   = array_keys($class['methods']);
                    //var_export($propertyNames);
                    //var_export($methodNames);
                    foreach ($class['methods'] as $methodName => $method) {
                        $this->parseMethodBody(
                            $className,
                            $methodName,
                            $propertyNames,
                            $methodNames,
                            $class['file'],
                            $method['startLine'],
                            $method['endLine']
                        );
                    }
                }
            }
        }
    }

    protected function buildLookupTables() {
        if (!empty($this->codeSummary['classes'])) {
            foreach ($this->codeSummary['classes'] as $className => $class) {
                //currently unused
                /*
                if (!empty($class['properties'])) {
                    foreach ($class['properties'] as $propertyName => $property) {
                        $this->propertyLookupTable[$propertyName][] = $className;
                    }
                }
                //*/
                if (!empty($class['methods'])) {
                    foreach ($class['methods'] as $methodName => $method) {
                        $this->methodLookupTable[$methodName][] = $className;
                    }
                }
            }
        }
    }

    /**
     * @param string  $className
     * @param string  $methodName
     * @param array   $propertyNames
     * @param array   $methodNames
     * @param string  $file
     * @param integer $startLine
     * @param integer $endLine
     */
    public function parseMethodBody(
        $className,
        $methodName,
        Array $propertyNames,
        Array $methodNames,
        $file,
        $startLine,
        $endLine
    ) {
        if ($className == '-') { // we are analyzing a function not a method
            if (substr($methodName, 0, strlen('dummyFunctionForFile_')) == 'dummyFunctionForFile_') {
                // the function has been introduced manually to encapsulate code in the global scope
                $callerName = str_replace('_', '.', substr($methodName, strlen('dummyFunctionForFile_'))) . '(File)';
            } else {
                $callerName = $methodName . $this->generateParametersForSignature($this->codeSummary['functions'][$methodName]['params']);
            }
        } else {
            //TODO: visibilities
            $callerName = $className . '::' . $methodName . $this->generateParametersForSignature($this->codeSummary['classes'][$className]['methods'][$methodName]['params']);
        }

        $callerNameWithoutParameterList = substr($callerName, 0, strpos($callerName, '('));
        
        if (!in_array($callerNameWithoutParameterList, $this->ignoreList)) {
            if ($this->debug) {
                echo 'phpCallGraph: analyzing ', $callerName, "\n";
            }
            $offset = $startLine - 1;
            $length = $endLine - $startLine + 1;

            //echo "\n$callerName defined in $file on line $offset\n";
            $this->driver->startFunction($offset, $file, $callerName);

            // obtain source code
            $memberCode = implode('', array_slice(file($file), $offset, $length));
            $memberCode = "<?php\nclass $className {\n" . $memberCode . "}\n?>\n";
            //echo $memberCode;

            $insideDoubleQuotedString = false;
            $lineNumber = $offset - 1;
            $blocksStarted = 0;
            // parse source code
            $tokens = token_get_all($memberCode);
            /*
            if ($methodName == '__construct') {
                print_r($tokens);
            }
            //*/

            //TODO: implement a higher level API for working with PHP parser tokens (e.g. TokenIterator)
            foreach ($tokens as $i => $token) {
                //TODO: obtain method signature directly from the source file
                if (is_array($token)) {
                    $lineNumber+= substr_count($token[1], "\n");
                }

                /*
                if (count($token) == 3) {
                    echo "\t", token_name($token[0]), "\n";
                    echo "\t\t", $token[1], "\n";
                } else {
                    echo "\t", $token[0], "\n";
                }
                //*/

                // skip call analysis for the method signature
                if ($blocksStarted < 2) {
                    // method body not yet started
                    if ($token[0] == '{') {
                        ++$blocksStarted;
                    }
                    continue;
                }

                if (!$insideDoubleQuotedString and $token == '"') {
                    $insideDoubleQuotedString = true;
                } elseif ($insideDoubleQuotedString and $token == '"') {
                    $insideDoubleQuotedString = false;
                } elseif (!$insideDoubleQuotedString and $token != '"') {
                    if ($token[0] == T_STRING
                        //and ($token[1] != $className or $tokens[$i - 2][0] == T_NEW )
                        //and $token[1] != $methodName
                    ) {
                        if (
                            !in_array($token[1], $propertyNames) //TODO: property name equals name of a function or method
                            and !in_array($token[1], $this->constants) //TODO: constant name equals name of a function or method
                            and $token[1] != 'true'
                            and $token[1] != 'false'
                            and $token[1] != 'null'
                        ) {
                            $peviousPreviousPreviousToken = $tokens[ $i - 3 ];
                            $peviousPreviousToken         = $tokens[ $i - 2 ];
                            $peviousToken                 = $tokens[ $i - 1 ];
                            $nextToken                    = $tokens[ $i + 1 ];
                            $tokenAfterNext               = $tokens[ $i + 2 ];

                            if ($nextToken[0] == T_DOUBLE_COLON) {
                                // beginning of a call to a static method
                                //nop
                                continue;
                            } elseif (
                                (
                                    $tokens[ $i - 4][0]                  == T_CATCH
                                    and $peviousPreviousPreviousToken[0] == T_WHITESPACE
                                    and $peviousPreviousToken            == '('
                                    and $peviousToken[0]                 == T_WHITESPACE
                                )
                                or
                                (
                                    $peviousPreviousPreviousToken[0] == T_CATCH
                                    and $peviousPreviousToken[0]     == T_WHITESPACE
                                    and $peviousToken                == '('
                                )
                                or
                                (
                                    $peviousPreviousToken[0] == T_CATCH
                                    and $peviousToken        == '('
                                )
                            ){
                                // catch block
                                continue;
                            } elseif ($peviousPreviousToken[0] == T_NEW){
                                // object creation
                                if (!$this->showExternalCalls) {
                                    continue;
                                }
                                $calleeClass = $token[1];
                                if (isset($this->codeSummary['classes'][$calleeClass])) {
                                    // find constructor method
                                    if (isset($this->codeSummary['classes'][$calleeClass]['methods']['__construct'])) {
                                        $calleeName = "$calleeClass::__construct"
                                            . $this->generateParametersForSignature($this->codeSummary['classes'][$calleeClass]['methods']['__construct']['params']);
                                    } elseif (isset($this->codeSummary['classes'][$calleeClass]['methods'][$calleeClass])) {
                                        $calleeName = "$calleeClass::$calleeClass"
                                            . $this->generateParametersForSignature($this->codeSummary['classes'][$calleeClass]['methods'][$calleeClass]['params']);
                                    } else {
                                        $calleeName = "$calleeClass::__construct()";
                                    }
                                    $calleeFile = $this->codeSummary['classes'][$calleeClass]['file'];
                                } else {
                                    // TODO: decide how this case should be handled (could be a PEAR class or a class of a PHP extension, e.g. GTK)
                                    //if ($this->showInternalFunctions)
                                    $calleeName = "$calleeClass::__construct()";
                                    $calleeFile = '';
                                }
                            } elseif (
                                (
                                    isset($peviousPreviousToken[1]) and $peviousPreviousToken[1] == '$this'
                                    and $peviousToken[0]     == T_OBJECT_OPERATOR
                                    and in_array($token[1], $methodNames)
                                )
                                or
                                (
                                    isset($peviousPreviousToken[1]) and $peviousPreviousToken[1] == 'self'
                                    and $peviousToken[0]     == T_DOUBLE_COLON
                                    and in_array($token[1], $methodNames)
                                )
                            ){
                                // internal method call ($this-> and self:: and $className::)
                                $calleeName = "$className::{$token[1]}" . $this->generateParametersForSignature($this->codeSummary['classes'][$className]['methods'][$token[1]]['params']);
                                $calleeFile = $file;
                            } elseif ($peviousToken[0] == T_OBJECT_OPERATOR) {
                                // external method call or property access
                                //TODO: what if a object holds another instance of its class
                                if (!$this->showExternalCalls) {
                                    continue;
                                }
                                if ($nextToken == '(' or ($nextToken[0] == T_WHITESPACE and $tokenAfterNext == '(')) {
                                    $calleeName = $token[1];
                                    if (
                                        isset($this->methodLookupTable[$calleeName])
                                        and count($this->methodLookupTable[$calleeName]) == 1
                                    ) {
                                        // there is only one class having a method with this name
                                        $calleeClass  = $this->methodLookupTable[$calleeName][0];
                                        $calleeParams =  $this->generateParametersForSignature(
                                            $this->codeSummary['classes'][$calleeClass]['methods'][$calleeName]['params']
                                            );
                                        $calleeFile   = $this->codeSummary['classes'][$calleeClass]['file'];
                                    } else {
                                        $calleeClass  = '';
                                        $calleeParams = '()';
                                        $calleeFile   = '';
                                    }
                                    $calleeName = "$calleeClass::$calleeName$calleeParams";
                                } else {
                                    // property access
                                    continue;
                                }
                            } elseif ($peviousToken[0] == T_DOUBLE_COLON){
                                // static external method call
                                if (!$this->showExternalCalls) {
                                    continue;
                                }
                                if ($nextToken != '(' and !($nextToken[0] == T_WHITESPACE and $tokenAfterNext == '(')) {
                                    // constant access
                                    continue;
                                }
                                $calleeClass  = $peviousPreviousToken[1];
                                $calleeMethod = $token[1];
                                $calleeFile = '';
                                $calleeParams = '()';
                                // parent::
                                if ($calleeClass == 'parent' and !empty($this->codeSummary['classes'][$className]['parentClass'])) {
                                    $calleeClass = $this->codeSummary['classes'][$className]['parentClass'];
                                }
                                if (isset($this->codeSummary['classes'][$calleeClass])) {
                                    $calleeFile = $this->codeSummary['classes'][$calleeClass]['file'];
                                    if (isset($this->codeSummary['classes'][$calleeClass]['methods'][$calleeMethod]['params'])) {
                                        $calleeParams = $this->generateParametersForSignature($this->codeSummary['classes'][$calleeClass]['methods'][$calleeMethod]['params']);
                                    }
                                }
                                $calleeName = "$calleeClass::$calleeMethod$calleeParams";
                                //TODO: handle self::myMethod(); $className::myMethod(); here => abolish internal method call case
                            } else {
                                // function call
                                $calledFunction = $token[1];
                                $calleeFile = '';
                                $calleeParams = '()';
                                
                                if (in_array($calledFunction, $this->internalFunctions)) {
                                    if (!$this->showInternalFunctions) {
                                        continue;
                                    }
                                } else {
                                    if (!$this->showExternalCalls) {
                                        continue;
                                    }
                                    if (isset($this->codeSummary['functions'][$calledFunction])) {
                                        $calleeFile = $this->codeSummary['functions'][$calledFunction]['file'];
                                        if (isset($this->codeSummary['functions'][$calledFunction]['params'])) {
                                            $calleeParams = $this->generateParametersForSignature($this->codeSummary['functions'][$calledFunction]['params']);
                                        }
                                    }
                                }
                                $calleeName = $calledFunction . $calleeParams;
                            }
                            //echo "\t", $calleeName, " called on line $lineNumber and defined in $calleeFile\n";
                            $this->driver->addCall($lineNumber, $calleeFile, $calleeName);
                        }
                    }
                } else {
                    //TODO: parse calls indside double quoted strings
                }
            }
            $this->driver->endFunction();
        }
    }

    public function generateParametersForSignature($parameters) {
        $result = '(';
        if (!empty($parameters)) {
            foreach($parameters as $parameterName => $parameter) {
                if ($parameter['byReference']) {
                    $result.= '&';
                }
                $result.= '$' . $parameterName . ', ';
            }
            $result = substr($result, 0, -2);
        }
        $result.= ')';
        return $result;
    }

    public function __toString() {
        return $this->driver->__toString();
    }

    public function save($file) {
        return file_put_contents($file, $this->__toString());
    }
}
?>
