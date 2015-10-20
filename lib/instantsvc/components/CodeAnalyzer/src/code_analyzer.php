<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

//***************************************************************************
//***************************************************************************
//**                                                                       **
//** iscCodeAnalyzer - searchs through source tree and collects infos      **
//**                   about found classes and files                       **
//**                                                                       **
//** Project: Web Services Description Generator                           **
//**                                                                       **
//** @package    CodeAnalyzer                                              **
//** @author     Stefan Marr <mail@stefan-marr.de>                         **
//** @copyright  2006 InstantSVC Team                                      **
//** @license    www.apache.org/licenses/LICENSE-2.0   Apache License 2.0  **
//**                                                                       **
//***************************************************************************
//***************************************************************************

//***** iscCodeAnalyzer *****************************************************
/**
 * searchs through source tree and collects infos about found classes
 * and files
 *
 * Some basic statistics are collected:
 *   - LoC
 *   - count of elements (classes, methods, ...)
 *   - Missing DocTags per element
 *   - used DocTags
 *
 * @TODO: correct folder names
 * @TODO: paths with slashes instead of backslashes
 * @TODO: static analysis should be able to handle multiple class declarations with the same name, although this may be bad design
 *        data structure could be changed into: $codeSummary['classes']['MyClass'][0]['file']
 *                                              $codeSummary['classes']['MyClass'][1]['file']
 *
 * @package    CodeAnalyzer
 * @author     Stefan Marr <mail@stefan-marr.de>
 * @copyright  2006 InstantSVC Team
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License 2.0
 */
class iscCodeAnalyzer {

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array<string,mixed>
     */
    protected $statsArray;

    /**
     * @var array(string => iscCodeAnalyzerFileDetails)
     */
    protected $flatStatsArray = array();

    /**
     * @var array<string,mixed>
     */
    protected $docuFlaws;

    /**
     * Decides wether debug information is printed
     * @var boolean
     */
    protected $debug = false;

    /**
     * PHP file with an autoload function which will be included into the sandbox
     * @var string 
     */
    protected $autoloadFile = '';

    /**
     * @param string $path
     */
    public function __construct($path = '.') {
        $this->path = $path;
    }

    /**
     * @return array(string=>mixed)
     */
    public function getCodeSummary() {
        return $this->docuFlaws;
    }

    /**
     * @return array<string,mixed>
     */
    public function getStats() {
        return $this->flatStatsArray;
    }

    /**
     * Enable or disable printing of debug information
     * @param boolean $enabled
     */
    public function setDebug( $enabled = true ) {
        $this->debug = $enabled;
    }

    /**
     * Sets a PHP file with an autoload function which will be included into the sandbox
     * @param string $filename Name of a PHP file with an autoload function
     * @return boolean success
     */
    public function setAutoloadFile( $filename ) {
        $returnValue = false;
        if ( !empty( $filename ) and is_file( $filename ) and is_readable( $filename ) ) {
            $this->autoloadFile = $filename;
            $returnValue = true;
        }
        return $returnValue;
    }

    /**
     * Starts collection of stats
     * Traverses the directory tree and collects statistical data
     * Doesn't include any file in current php process
     */
    public function collect() {
        $this->parseDir($this->path, $this->statsArray);
        $this->flatStatsArray = $this->flatoutStatsArray($this->statsArray, '');
        $this->inspectFiles(null);
    }

    /**
     * Parse the given directory recursivly
     *
     * @param string $path
     * @param array $statsArray
     */
    protected function parseDir($path, &$statsArray) {
        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while (($file = readdir($dir)) !== false) {
                    if ($file != '..' && $file != '.' &&
                        $file != '.svn' && $file != 'CVS') {
                        if (is_dir($path.'/'.$file)) {
                            $statsArray[$file] = array();
                            $this->parseDir($path.'/'.$file,$statsArray[$file]);
                        }
                        else {
                            $statsArray[$file] = new iscCodeAnalyzerFileDetails
                                                        ($path.'/'.$file);
                        }
                    }
                }
                closedir($dir);
            }
        }
    }

    /**
     * Convert statsArray to a flat one dimensional array
     * @param array(string=>mixed) $array
     * @param string $basekey
     * @return array(string=>mixed)
     */
    protected function flatoutStatsArray($array, $basekey) {
        $result = array();
        $dirDetails = new iscCodeAnalyzerFileDetails($basekey);
        $dirDetails->mimeType = 'folder';
        $result[$basekey] = $dirDetails;
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $r = $this->flatoutStatsArray($value, $key);
                    $first = true;
                    foreach ($r as $k => $v) {
                        if ($first) {
                            $first = false;
                            $dirDetails->fileSize += $v->fileSize;
                            $dirDetails->linesOfCode += $v->linesOfCode;
                        }
                        $result[$basekey.'\\'.$k] = $v;
                    }
                }
                else {
                    $result[$basekey.'\\'.$key] = $value;
                    $dirDetails->fileSize += $value->fileSize;
                    $dirDetails->linesOfCode += $value->linesOfCode;
                }
            }
        }
        return $result;
    }

    /**
     * Collects informations about classes, functions by spawning a new
     * php process for each file
     *
     * @param string[] $files array of filenames
     */
    public function inspectFiles($files) {

        $this->docuFlaws = array();
        $this->docuFlaws['classes'] = array();
        $this->docuFlaws['functions'] = array();
        $this->docuFlaws['interfaces'] = array();

        if ($files == null) {
            $files = $this->flatStatsArray;
        }

        foreach ($files as $key => $file) {
            $filename = null;
            if (is_string($file)) {
                $filename = $file;
            }
            //TODO: may be it's better to use a php -l check here like in the class loader
            elseif ($file->mimeType == 'application/x-httpd-php') {
                $filename = $file->fileName;
            }

            if (!empty($filename)) {
                $filename = strtr($filename, DIRECTORY_SEPARATOR, '/');

                $result = self::summarizeInSandbox($filename, $this->autoloadFile, $this->debug);

                if (is_array($result)) {
                    //$this->docuFlaws['classes'] = array_merge_recursive($this->docuFlaws['classes'],
                    $this->docuFlaws['classes'] = array_merge($this->docuFlaws['classes'],
                                                          $result['classes']);
                    //$this->docuFlaws['functions'] = array_merge_recursive($this->docuFlaws['functions'],
                    $this->docuFlaws['functions'] = array_merge($this->docuFlaws['functions'],
                                                          $result['functions']);
                    //$this->docuFlaws['interfaces'] = array_merge_recursive($this->docuFlaws['interfaces'],
                    $this->docuFlaws['interfaces'] = array_merge($this->docuFlaws['interfaces'],
                                                          $result['interfaces']);
                    if (is_object($file)) {
                        $file->countClasses = count($result['classes']);
                        $file->countInterfaces = count($result['interfaces']);
                        $file->countFunctions = count($result['functions']);
                        $this->flatStatsArray[$key] = $file;
                    }
                }
            }
        }
        $this->buildInheritanceTree();
        $this->summarizeProject();
    }

    protected function buildInheritanceTree() {
        $classes = &$this->docuFlaws['classes'];
        foreach ($classes as $className => $class) {
            if ($class['parentClass'] != null) {
                if (isset($classes[$class['parentClass']])) {
                    $classes[$class['parentClass']]['children'][] = $className;
                    ++$classes[$class['parentClass']]['childrenCount'];
                }
            }
        }
    }

    /**
     * Calls summarizeFile in a new php process.
     * @param string $filename PHP file to analyze
     * @param string $autoloadFile PHP file with an autoload function which will be included into the sandbox
     * @param string $debug Decide wether to print debug information
     * @return array(string => array)
     */
    public static function summarizeInSandbox($filename, $autoloadFile = '', $debug = false) {
        $return = null;

        if ( $debug ) {
            echo 'CodeAnalyzer: inspecting ', $filename, "\n";
        }

        // adapt the php.ini file for the sandbox to the current PHP configuration
        $functionWhiteList = array(
            'set_include_path',
            'class_exists',
            'ob_start',
            'serialize',
            'ob_end_clean',
            'chr',
            'flush',
            'get_declared_classes',
            'realpath',
            'get_declared_interfaces',
            'get_defined_functions',
            'substr_count',
            'is_object',
            'count',
            'strlen',
            'dirname',
            'array_key_exists',
            'preg_match',
            'sizeof',
            'strtolower',
            'file_exists',
            'is_array',
            'array_merge',
            'explode',
            'trim',
            'substr',
            'is_string',
            'method_exists',
            'strrpos',
            'trigger_error',
            'preg_split',
            'function_exists',
            'ucfirst',
            // may be needed for autoload implementations
            '__autoload',
            'define',
            'defined',
            'get_include_path',
            'glob',
            'in_array',
            'is_dir',
            'is_readable',
            'strpos',
            // for analysis of phpDocumentor
            'extension_loaded',
            'phpversion',
            'version_compare',
            // only for debugging purposes!!!1
            /*
            'file_put_contents',
            'var_export',
            //*/
        );

        $classWhiteList = array(
            'ezcBase',
            'ezcBaseStruct',
            'ezcReflectionApi',
            'ezcReflectionClass',
            'ezcReflectionClassType',
            'ezcReflectionFunction',
            'ezcReflectionMethod',
            'iscCodeAnalyzer',
            'Exception',
            'ReflectionException',
            'Reflection',
            'ReflectionFunctionAbstract',
            'ReflectionFunction',
            'ReflectionParameter',
            'ReflectionMethod',
            'ReflectionClass',
            'ReflectionProperty',
        );

        $functions = get_defined_functions();
        $functionBlackList = array_diff($functions['internal'], $functionWhiteList);
        $classBlackList    = array_diff(get_declared_classes(), $classWhiteList);

        $iniFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'php.ini';
        $configuration = file_get_contents($iniFile);
        $configuration = preg_replace(
            '/disable_functions.*?\n/',
            'disable_functions = ' . implode(', ', $functionBlackList) . "\n",
             $configuration
        );
        $configuration = preg_replace(
            '/disable_classes.*?\n/',
            'disable_classes = ' . implode(', ', $classBlackList) . "\n",
            $configuration
        );
        $newIniFile = 'php.ini.for-code-analyzer-sandbox';
        $useNewIniFile = file_put_contents($newIniFile, $configuration);
        if ($useNewIniFile) {
            $iniFile = $newIniFile;
        }

        // create a second php process
        $pipeDesc = array(
           0 => array('pipe', 'r'),  //in, child reads from
           1 => array('pipe', 'w'),  //out, child writes to
           2 => array('pipe', 'w')   //err, child writes to
        );

        $cmd = 'php -c '.escapeshellarg($iniFile);
        $process = proc_open($cmd, $pipeDesc, $pipes);

        if (is_resource($process)) {
            // generate include statements for the sandbox
            $requiredClasses = array(
                'ezcReflectionClass',
                'ezcReflectionType',
                'ezcReflectionClassType',
                'ezcReflectionApi',
                'ezcReflectionDocParser',
                'ezcReflectionPhpDocParser',
                'ezcReflectionProperty',
                'ezcReflectionDocTagFactory',
                'ezcReflectionDocTag',
                'ezcReflectionDocTagvar',
                'ezcReflectionTypeMapper',
                'ezcReflectionTypeFactory',
                'ezcReflectionTypeFactoryImpl',
                'ezcReflectionAbstractType',
                'ezcReflectionPrimitiveType',
                'ezcReflectionArrayType',
                'ezcReflectionParameter',
                'ezcReflectionFunction',
                'ezcReflectionMethod',
                'iscCodeAnalyzer',
            );
            $includes = '';
            foreach ($requiredClasses as $requiredClassName) {
                $requiredClass = new ezcReflectionClass($requiredClassName);
                if ($requiredClass->isUserDefined()) {
                    //echo $requiredClassName, "\n";
                    $requiredFile = addslashes($requiredClass->getFileName());
                    $includes .= "require_once '$requiredFile';\n";
                }
            }

            // include a file with an __autoload function provided by the user
            if (!empty($autoloadFile) and is_file($autoloadFile) and is_readable($autoloadFile)) {
                $includes .= "require_once '$autoloadFile';\n";
            }

            $include_path = addslashes(get_include_path());
            $fileToInspect = addslashes($filename);

            // generate PHP code for the sandbox
            $phpCommands = <<<SANDBOXCODE
<?php
//file_put_contents("autoload.log", "$fileToInspect,\\n", FILE_APPEND);

set_include_path("$include_path");

// these explicit includes allow scanning files which define an own __autoload function
$includes

// the ezc autoloader is only needed when analyzing eZ Components based applications, e.g. InstantSVC or phpCallGraph
if (!function_exists('__autoload')) {
    // try to find an SVN, Release or PEAR version of base.php
    foreach (array('Base/src/base.php', 'Base/base.php', 'ezc/Base/base.php') as \$ezcBaseFileToInclude) {
        if (!in_array('ezcBase', get_declared_classes())) {
            @include_once \$ezcBaseFileToInclude;
        } else {
            break;
        }
    }
    // remove the global variable used in the foreach loop
    unset(\$ezcBaseFileToInclude);

    // define an __autoload function which is automatically called in case a class
    // is used which hasn't been declared
    function __autoload( \$className ) {
        //file_put_contents("autoload.log", "'\$className',\\n", FILE_APPEND);
        ezcBase::autoload( \$className );
    }
}

ob_start();
\$iscCodeAnalyzerOutput = serialize(iscCodeAnalyzer::summarizeFile("$fileToInspect"));
ob_end_clean();
echo '#-#-#-#-#';
echo \$iscCodeAnalyzerOutput;
echo '#-#-#-#-#';
echo chr(4); // necessary to avoid deadlook
flush();
exit();
?>
SANDBOXCODE;
            //file_put_contents('sandboxes.php', $phpCommands, FILE_APPEND);
            // for testing use: php -c php.ini.for-code-analyzer-sandbox sandboxes.php

            //pipe commands to new process and close pipe to start processing by php
            fwrite($pipes[0], $phpCommands);
            fclose($pipes[0]);

            //get result and close return and error pipe
            $result = '';
            //sometimes pipe doesnt get eof on win32, so we have to work around
            //$result = stream_get_contents($pipes[1]);

            while(!feof($pipes[1])) {
                $read = fread($pipes[1], 4096);

                //break on error
                if ($read === false) break;
                $result .= $read;

                //sometimes we dont get a EOF so lets test for self send EOT
                if (strlen($read) > 0 and $read{strlen($read)-1} == chr(4)) {
                    break;
                }

                // print errors in debug mode
                if ( $debug and strpos($read, "##ERR##\n") !== false ) {
                    //TODO: provide error message in the output data structure
                    echo 'Error in code analyzer sandbox: ', $read, "\n";
                }
                

                //another time fatal errors will bring us to hang
                if (strpos($read, "##ERR##\nFatal error: ") !== false or strpos($read, "##ERR##\nParse error: ") !== false) {
                    break;
                }
            }

            /*
            $error = stream_get_contents($pipes[2]);
            echo $error, "\n";
            //*/

            fclose($pipes[1]);
            fclose($pipes[2]);

            // pipes are closed to avoid a deadlock
            proc_close($process);

            if ($useNewIniFile) {
                unlink($newIniFile);
            }

            if ($result == "Could not startup.\n") {
                throw new Exception('The PHP commandline interpreter could not be started. It failed with the message \'Could not startup\'. Try removing extensions like PHP-Gtk from the php.ini used by your PHP CLI.');
            }

            //echo '$filename = ', var_export($filename, true), ";\n";
            //echo '$result   = ', var_export($result, true), ";\n";

            $arr = split('#-#-#-#-#', $result);

            if (isset($arr[1])) {
                $old = error_reporting(0);
                $return = unserialize($arr[1]);
                error_reporting($old);
            }
        }

        return $return;
    }

    /**
     * Collect summary for given file
     *
     * @param string $fileName
     * @return array(string => array)
     */
    public static function summarizeFile($fileName) {
        ob_start();
        try {
            require_once $fileName;
        }
        catch (Exception $e)
        {
            unset($e);
        }
        ob_end_clean();


        $classes = array();
        $decClasses = get_declared_classes();
   
        foreach ($decClasses as $class) {
            $class = new ReflectionClass($class);
            if ($class->getFileName() == realpath($fileName)) {
                $classes[] = $class->getName();
            }
        }
        $classes = self::summarizeClasses($classes);

        $inters = array();
        $interfaces = get_declared_interfaces();
        foreach ($interfaces as $inter) {
            $inter = new ReflectionClass($inter);
            if ($inter->getFileName() == realpath($fileName)) {
                $inters[] = $inter->getName();
            }
        }
        $inters = self::summarizeInterfaces($inters);

        $functs = array();
        $functions = get_defined_functions();
        foreach ($functions['user'] as $func) {
            $func = new ReflectionFunction($func);
            if ($func->getFileName() == realpath($fileName)) {
               $functs[] = $func->getName();
            }
        }
        $functs = self::summarizeFunctions($functs);

        return array('classes' => $classes, 'interfaces' => $inters,
                     'functions' => $functs);
    }

    /**
     * Counts the classes which are able to access a method.
     *
     * A private method is seen by no other class.
     * A protected method is seen by all subclasses.
     * A public method is seen by all other classes.
     *
     * @param array(string => mixed) $classes
     * @param int $methodCount
     * @return int
     */
    protected static function countClassesSeeingMethods($classes, &$methodCount) {
        $methodsVisibleToOthers = 0;
        $methodCount = 0;
        $classCount = count($classes);
        foreach ($classes as $className => $class) {
            foreach ($class['methods'] as $method) {
                ++$methodCount;

                //simple
                if ($method['isPublic']) {
                    $methodsVisibleToOthers += $classCount - 1;
                }

                //nothing
                if ($method['isPrivate']) { }

                //complicated
                if ($method['isProtected']) {
                    $methodsVisibleToOthers += self::countSubclasses($classes,
                                                                    $className);
                }
            }
        }
        return $methodsVisibleToOthers;
    }

    /**
     * Counts the classes which are able to access a property.
     *
     * A private property is seen by no other class.
     * A protected property is seen by all subclasses.
     * A public property is seen by all other classes.
     *
     * @param array(string => mixed) $classes
     * @param int $propCount
     * @return int
     */
    protected static function countClassesSeeingProperties($classes, &$propCount) {
        $propsVisibleToOthers = 0;
        $propCount = 0;
        $classCount = count($classes);
        foreach ($classes as $className => $class) {
            foreach ($class['properties'] as $prop) {
                ++$propCount;
                //simple
                if ($prop['isPublic']) {
                    $propsVisibleToOthers += $classCount - 1;
                }

                //nothing
                if ($prop['isPrivate']) { }

                //complicated
                if ($prop['isProtected']) {
                    $propsVisibleToOthers += self::countSubclasses($classes,
                                                                    $className);
                }
            }
        }
        return $propsVisibleToOthers;
    }

    /**
     * Counts all inherited methods
     *
     * @param array(string => mixed) $classes
     * @return int
     */
    protected  static function countInheritedMethods($classes) {
        $i = 0;
        foreach ($classes as $class) {
            foreach ($class['methods'] as $method) {
                if ($method['isInherited']) {
                    ++$i;
                }
            }
        }
        return $i;
    }

    /**
     * Counts all overridden methods
     *
     * @param array(string => mixed) $classes
     * @return int
     */
    protected static function countOverriddenMethods($classes) {
        $overridden = 0;
        foreach ($classes as $class) {
            foreach ($class['methods'] as $method) {
                if ($method['isOverridden']) {
                    ++$overridden;
                }
            }
        }
        return $overridden;
    }


    /**
     * Counts all possible overriddes
     *
     * Sum of ($newMethods * $subClasses) for all methods
     *
     * @param array(string => mixed) $classes
     * @return int
     */
    public static function countPossibleOverriddes($classes) {
        $pos = 0;
        foreach ($classes as $className => $class) {
            $new = 0;
            foreach ($class['methods'] as $mName => $method) {
                if ($method['isIntroduced'] and !$method['isFinal']) {
                    ++$new;
                }
            }
            $pos += $new * self::countSubclasses($classes, $className);
        }
        return $pos;
    }

    /**
     * Counts all classes which are inherited from the given class
     *
     * @param array(string => mixed) $classes
     * @param string $class
     * @return int
     */
    public static function countSubclasses($classes, $class) {
        $subclasses = count($classes[$class]['children']);
        foreach ($classes[$class]['children'] as $childClass) {
            $subclasses += self::countSubclasses($classes, $childClass);
        }
        return $subclasses;
    }

    public static function collectMethodsStats($classes) {
        $min = 0;
        $max = 0;
        $avg = 0;
        $mCount = 0;

        foreach ($classes as $class) {
            $mCount += count($class['methods']);
            if ($min == 0) {
                $min = count($class['methods']);
            }
            else {
                $min = min($min, count($class['methods']));
            }
            $max = max(count($class['methods']), $max);
        }
        $avg = (count($classes) > 0)? $mCount / count($classes) : 0;
        return array('min'=>$min, 'avg'=>$avg, 'max'=>$max);
    }

    public function summarizeProject() {
        $project = array();

        $classes = &$this->docuFlaws['classes'];

        $methodCount = 0;
        $mv = self::countClassesSeeingMethods($classes, $methodCount);
        if ($methodCount > 0 && count($classes) > 1) {
            $project['MHF'] = 1 - ($mv / (count($classes) - 1) / $methodCount);
        }
        else {
            $project['MHF'] = 1;
        }

        $attrCount = 0;
        $av = self::countClassesSeeingProperties($classes, $attrCount);
        if ($attrCount > 0 && count($classes) > 1) {
            $project['AHF'] = 1 - ($av / (count($classes) - 1) / $attrCount);
        }
        else {
            $project['AHF'] = 1;
        }

        $inM = self::countInheritedMethods($classes);
        $project['MIF'] = ($methodCount > 0)? $inM / $methodCount : 0;
        

        $over = self::countOverriddenMethods($classes);
        $posOver = self::countPossibleOverriddes($classes);
        $project['PF'] = ($posOver == 0) ? 0 : $over / $posOver;

        $project['methods'] = self::collectMethodsStats($classes);
        $project['functions'] = $this->collectFunctionStats();
        $project['classes'] = $this->collectClassStats();

        if ($project['functions']['locSum'] > 0) {
            $project['dbcRatio'] = ($project['classes']['lodbSum'] +
                                    $project['functions']['lodbSum']) /
                                    $project['functions']['locSum'];
        }
        else {
            $project['dbcRatio'] = 0;
        }

        $this->docuFlaws['project'] = $project;
    }

    protected function collectClassStats() {
        $min = 0;
        $max = 0;
        $avg = 0;
        $cCount = 0;
        $fileC = 0;
        foreach ($this->flatStatsArray as $file) {
            $cCount += $file->countClasses;
            if ($min == 0) {
                $min = $file->countClasses;
            }
            else {
                $min = min($min, $file->countClasses);
            }
            $max = max($max, $file->countClasses);
            if ($file->countClasses > 0) {
                ++$fileC;
            }
        }

        if ($fileC > 0)
            $avg = $cCount / $fileC;

        $abstractClasses = 0;
        $rootClasses = 0;
        $leafClasses = 0;
        $ditMax = 0;
        $ditSum = 0;
        $lodbSum = 0;
        $locSum = 0;
        foreach ($this->docuFlaws['classes'] as $class) {
            $ditMax = max($class['DIT'], $ditMax);
            $ditSum += $class['DIT'];
            if ($class['isAbstract']) { ++$abstractClasses; }
            if (empty($class['parentClass'])) { ++$rootClasses; }
            if ($class['childrenCount'] < 1) { ++$leafClasses; }
            $locSum += $class['LoC'];
            $lodbSum += $class['LoDB'];
        }
        $ditAvg = ($cCount > 0)? $ditSum / $cCount : 0;
        return array('min' => $min, 'max' => $max, 'avg' => $avg,
                     'DITmax' => $ditMax, 'DITavg' => $ditAvg,
                     'leaf' => $leafClasses, 'root' => $rootClasses,
                     'abstract' => $abstractClasses, 'locSum' => $locSum,
                     'lodbSum' => $lodbSum);
    }

    protected function collectFunctionStats() {
        $min = 0;
        $max = 0;
        $avg = 0;
        $fCount = 0;
        $fileC = 0;
        foreach ($this->flatStatsArray as $file) {
            $fCount += $file->countFunctions;
            if ($min == 0) {
                $min = $file->countFunctions;
            }
            else {
                $min = min($min, $file->countFunctions);
            }
            $max = max($max, $file->countFunctions);
            if ($file->countFunctions > 0) {
                ++$fileC;
            }
        }
        if ($fileC > 0)
            $avg = $fCount / $fileC;

        $paramMin = -1;
        $paramMax = 0;
        $paramAvg = 0;
        $fCount = 0;
        $pCount = 0;

        $locMin = -1;
        $locMax = 0;
        $locAvg = 0;
        $locSum = 0;

        $lodbMin = -1;
        $lodbMax = 0;
        $lodbAvg = 0;
        $lodbSum = 0;
        foreach ($this->docuFlaws['functions'] as $func) {
            if ($paramMin == -1) { $paramMin = $func['paramCount']; }
            else { $paramMin = min($paramMin, $func['paramCount']); }

            $paramMax = max($paramMax, $func['paramCount']);
            $pCount += $func['paramCount'];
            ++$fCount;

            if ($locMin == -1) { $locMin = $func['LoC']; }
            else { $locMin = min($locMin, $func['LoC']); }

            $locMax = max($locMax, $func['LoC']);
            $locSum += $func['LoC'];

            if ($lodbMin == -1) { $lodbMin = $func['LoDB']; }
            else { $lodbMin = min($lodbMin, $func['LoDB']); }

            $lodbMax = max($lodbMax, $func['LoDB']);
            $lodbSum += $func['LoDB'];
        }
        foreach ($this->docuFlaws['classes'] as $class) {
            foreach ($class['methods'] as $func) {
                if ($paramMin == -1) { $paramMin = $func['paramCount']; }
                else { $paramMin = min($paramMin, $func['paramCount']); }

                $paramMax = max($paramMax, $func['paramCount']);
                $pCount += $func['paramCount'];
                ++$fCount;

                if ($locMin == -1) { $locMin = $func['LoC']; }
                else { $locMin = min($locMin, $func['LoC']); }

                $locMax = max($locMax, $func['LoC']);
                $locSum += $func['LoC'];

                if ($lodbMin == -1) { $lodbMin = $func['LoDB']; }
                else { $lodbMin = min($lodbMin, $func['LoDB']); }

                $lodbMax = max($lodbMax, $func['LoDB']);
                $lodbSum += $func['LoDB'];
            }
        }
        if ($fCount > 0) {
            $locAvg = $locSum / $fCount;
            $lodbAvg = $lodbSum / $fCount;
            $paramAvg = $pCount / $fCount;
        }
        else {
            $locAvg = 0;
            $lodbAvg = 0;
            $paramAvg = 0;
        }
        return array('min' => $min, 'max' => $max, 'avg' => $avg,
                     'paramMin' => $paramMin, 'paramMax' => $paramMax,
                     'paramAvg' => $paramAvg, 'locMin' => $locMin,
                     'locMax' => $locMax, 'locAvg' => $locAvg,
                     'lodbMin' => $lodbMin, 'lodbMax' => $lodbMax,
                     'lodbAvg' => $lodbAvg, 'locSum' => $locSum,
                     'lodbSum' => $lodbSum);
    }

    /**
     * Retrieves all information from the class signature
     *
     * @param iscReflectionClassType $class
     * @return array(string => mixed)
     */
    public static function summarizeClassSignature($class) {
        //Collect Class-Tags
        $tags = $class->getTags();
        foreach ($tags as $tag) {
            $result['tags'][] = $tag->getName();
        }

        //Collect special class info
        $result['file'] = $class->getFileName();
        $result['LoDB'] = substr_count($class->getDocComment(), "\n");
        $result['isWebService'] = $class->isTagged('webservice');
        $result['isInternal'] = $class->isInternal();
        $result['isAbstract'] = $class->isAbstract();
        $result['isFinal'] = $class->isFinal();
        $result['isInterface'] = $class->isInterface();
        $result['startLine'] = $class->getStartLine();
        $result['endLine'] = $class->getEndLine();
        $result['LoC'] = $class->getEndLine() - $class->getStartLine();

        $result['interfaces'] = array();
        $interfaces = $class->getInterfaces();
        foreach ($interfaces as $inter) {
            $result['interfaces'][] = $inter->getName();
        }

        $result['DIT'] = 1;
        if ($class->getParentClass() != null) {
            $result['parentClass'] = $class->getParentClass()->getName();

            $parent = $class->getParentClass();
            while ($parent != null) {
                ++$result['DIT'];
                $parent = $parent->getParentClass();
            }
        }
        else {
            $result['parentClass'] = null;
        }
        $result['modifiers'] = $class->getModifiers();
        return $result;
    }

    /**
     * Retrieve all Information about defined properties of a class
     *
     * @param iscReflectionClassType $class
     * @return array(string => mixed)
     */
    public static function summarizeClassProperties($class) {
        $props = $class->getProperties();
        $result = array();
        foreach ($props as $property) {
            // echo $class->getFileName(), "\n", $class->getName(), '::', $property->getName(), "\n";
            if (is_object($property->getType())) {
               $result[$property->getName()]['type'] =
                                               $property->getType()->toString();
               $result[$property->getName()]['docuMissing'] = false;
            }
            else {
               $result[$property->getName()] = null;
               $result[$property->getName()]['docuMissing'] = true;
            }

           $result[$property->getName()]['LoDB'] =
                                 substr_count($property->getDocComment(), "\n");

           $result[$property->getName()]['modifiers'] =
                                                      $property->getModifiers();
           $result[$property->getName()]['isDefault'] = $property->isDefault();

           $result[$property->getName()]['isPrivate'] = $property->isPrivate();
           $result[$property->getName()]['isPublic'] = $property->isPublic();
           $result[$property->getName()]['isProtected'] = $property->isProtected();

           if ($property->isPrivate())
           { $result[$property->getName()]['visibility'] = 'private'; }
           elseif ($property->isPublic())
           { $result[$property->getName()]['visibility'] = 'public'; }
           elseif ($property->isProtected())
           { $result[$property->getName()]['visibility'] = 'protected'; }

           $result[$property->getName()]['isStatic'] = $property->isStatic();
        }
        return $result;
    }

    /**
     * Retrieve all information about parameters of a method or a function
     *
     * @param iscReflectionFunction $method
     * @param integer $paramFlaws
     * @return array(string => mixed)
     */
    public static function summarizeFunctionParameters($method, &$paramFlaws) {
        $params = $method->getParameters();
        $paramFlaws = 0;
        $result = array();
        foreach ($params as $param) {
            if (is_object($param->getType())) {
                $result[$param->getName()]['type'] = $param->getType()->toString();
            }
            else {
                $result[$param->getName()]['type'] = null;
            }

            if ($param->getType() == null) {
                $paramFlaws++;
            }

            $result[$param->getName()]['isOptional'] = $param->isOptional();
            $result[$param->getName()]['byReference'] = $param->isPassedByReference();
            if ($param->isOptional()) {
                $result[$param->getName()]['hasDefault'] = $param->isDefaultValueAvailable();
                $result[$param->getName()]['defaultValue'] = $param->getDefaultValue();
            }
        }
        return $result;
    }

    /**
     * Retrieve all information of all methods of a class
     *
     * @param iscReflectionClassType $class
     * @param integer $missingMethodComments
     * @param integer $missingParamTypes
     * @return array(string => mixed)
     */
    public static function summarizeClassMethods($class,
                                                 &$missingMethodComments,
                                                 &$missingParamTypes) {
        $methods = $class->getMethods();
        $missingMethodComments = 0;
        $missingParamTypes = 0;
        $result = array();
        foreach ($methods as $method) {
            //echo $class->getFileName(), ' ', $class->getName(), '::', $method->getName(), "\n";

            //Collect method tags
            $tags = $method->getTags();
            foreach ($tags as $tag) {
                $result[$method->getName()]['tags'][] = $tag->getName();
            }

            //Collect more infos about this method
            $result[$method->getName()]['isInternal'] = $method->isInternal();
            $result[$method->getName()]['isAbstract'] = $method->isAbstract();
            $result[$method->getName()]['isFinal'] = $method->isFinal();
            $result[$method->getName()]['isPublic'] = $method->isPublic();
            $result[$method->getName()]['isPrivate'] = $method->isPrivate();
            $result[$method->getName()]['isProtected'] = $method->isProtected();
            $result[$method->getName()]['isStatic'] = $method->isStatic();
            $result[$method->getName()]['modifiers'] = $method->getModifiers();
            $result[$method->getName()]['isConstructor'] = $method->isConstructor();
            $result[$method->getName()]['isDestructor'] = $method->isDestructor();
            $result[$method->getName()]['isOverridden'] = $method->isOverridden();
            $result[$method->getName()]['isInherited'] = $method->isInherited();
            $result[$method->getName()]['isIntroduced'] = $method->isIntroduced();

            if ($method->isPublic())
            { $result[$method->getName()]['visibility'] = 'public'; }
            elseif ($method->isProtected())
            { $result[$method->getName()]['visibility'] = 'protected'; }
            elseif ($method->isPrivate())
            { $result[$method->getName()]['visibility'] = 'private'; }


            $result[$method->getName()]['LoDB'] =
                                   substr_count($method->getDocComment(), "\n");
            if ($result[$method->getName()]['LoDB'] < 1) {
               $missingMethodComments++;
            }

            if (is_object($method->getReturnType())) {
               $result[$method->getName()]['return'] = $method->getReturnType()->toString();
            } else {
               $result[$method->getName()]['return'] = null;
            }
            $result[$method->getName()]['isWebMethod'] = $method->isTagged('webmethod');
            $result[$method->getName()]['isRestMethod']
                                              = $method->isTagged('restmethod');

            $result[$method->getName()]['startLine'] = $method->getStartLine();
            $result[$method->getName()]['endLine'] = $method->getEndLine();
            $result[$method->getName()]['LoC'] = $method->getEndLine() - $method->getStartLine();

            $result[$method->getName()]['paramCount'] = $method->getNumberOfParameters();
            $result[$method->getName()]['reqParamCount'] = $method->getNumberOfRequiredParameters();

            $paramFlaws = 0;
            $result[$method->getName()]['params'] =
                     self::summarizeFunctionParameters($method, $paramFlaws);

            $missingParamTypes += $paramFlaws;
            $result[$method->getName()]['paramflaws'] = $paramFlaws;
        }
        return $result;
    }

    /**
     * Will build summary of all code constructs and their meta data
     *
     * Is called by inc.iscCodeAnalyzer.php and returns an array structur
     * serialized by serialize() as string
     *
     * @param $classes
     * @return string
     */
    public static function summarizeClasses($classes) {
        $result = array();
        foreach ($classes as $className) {
            $class = new ezcReflectionClassType($className);

            $result[$className] = self::summarizeClassSignature($class);
            $result[$className]['interfaceCount'] = count($result[$className]['interfaces']);

            $result[$className]['properties'] = self::summarizeClassProperties($class);
            $result[$className]['propertyCount'] = count($result[$className]['properties']);

            $missingMethodComments = 0;
            $missingParamTypes = 0;
            $result[$className]['methods'] = self::summarizeClassMethods($class,
                                                         $missingMethodComments,
                                                         $missingParamTypes);
            $result[$className]['methodCount'] = count($result[$className]['methods']);
            $result[$className]['nonePrivateMethods'] = 0;
            $result[$className]['inheritedMethods'] = 0;
            $result[$className]['overriddenMethods'] = 0;
            foreach ($result[$className]['methods'] as $method) {
                if (!$method['isPrivate']) {
                    ++$result[$className]['nonePrivateMethods'];
                }
                if ($method['isOverridden']) {
                    ++$result[$className]['overriddenMethods'];
                }
                if ($method['isInherited']) {
                    ++$result[$className]['inheritedMethods'];
                }
            }

            $result[$className]['missingMethodComments'] = $missingMethodComments;
            $result[$className]['missingParamTypes'] = $missingParamTypes;
            $result[$className]['children'] = array();
            $result[$className]['childrenCount'] = 0;
        }
        return $result;
    }

    /**
     * Collects all information about the interfaces given
     * @param string[] $interfaces
     * @return array(string=>mixed)
     */
    public static function summarizeInterfaces($interfaces) {
        return self::summarizeClasses($interfaces);
    }


    /**
     * Collects all information about the functions given
     * @param string[] $functions
     * @return array(string=>mixed)
     */
    public static function summarizeFunctions($functions) {
        $functs = array();
        foreach ($functions as $funcName) {
            $func = new ezcReflectionFunction($funcName);
            $functs[$funcName]['comment']       = (strlen($func->getDocComment()) > 10);
            $functs[$funcName]['file']          = $func->getFileName();
            $functs[$funcName]['LoDB']          = substr_count($func->getDocComment(), "\n");
            $functs[$funcName]['startLine']     = $func->getStartLine();
            $functs[$funcName]['endLine']       = $func->getEndLine();
            $functs[$funcName]['LoC']           = $func->getEndLine() - $func->getStartLine();

            $functs[$funcName]['paramCount']    = $func->getNumberOfParameters();
            $functs[$funcName]['reqParamCount'] = $func->getNumberOfRequiredParameters();

            if (is_object($func->getReturnType())) {
                $functs[$funcName]['return'] = $func->getReturnType()->toString();
            } else {
                $functs[$funcName]['return'] = null;
            }

            $tags = $func->getTags();
            foreach ($tags as $tag) {
                if (is_object($tag)) {
                   $functs[$funcName]['tags'][] = $tag->getName();
                }
            }

            //Collect paramter infos
            $paramFlaws = 0;
            $functs[$funcName]['params'] =
                          self::summarizeFunctionParameters($func, $paramFlaws);

            $functs[$funcName]['paramflaws'] = $paramFlaws;
        }

        return $functs;
    }
}

?>
