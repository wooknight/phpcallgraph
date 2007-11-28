<?php
// set up include path
set_include_path(
    realpath(dirname(__FILE__) . '/../src') . PATH_SEPARATOR
    . realpath(dirname(__FILE__) . '/../lib/ezcomponents-instantsvc/components') . PATH_SEPARATOR
    . ini_get( "include_path" ) . PATH_SEPARATOR
    . realpath(dirname(__FILE__) . '/../lib/pear')
);

// configure eZ Components autoloader
// required because of PHPCallGraphCli using eczConsoleTools
// and PHPCallGraph leveraging InstantSVC CodeAnalyzer and Extended Reflection API
require_once 'Base/src/base.php';
function __autoload( $className ) { ezcBase::autoload( $className ); }

// include PHPCallGraph library
require_once 'PHPCallGraph.php';

// configure output driver
require_once 'drivers/GraphVizDriver.php';
$driver = new GraphVizDriver('png');

// configure generator
$phpcg = new PHPCallGraph($driver);

// change this path to your eZ Components SVN working copy
$ezcDir = '../../../../instantsvc/ezc-svn/trunk';

// destination directory for the output files
$targetDir = '../../website/htdocs/examples/ezcomponents';

// locate the component directories
$componentDirs = glob($ezcDir . '/*', GLOB_ONLYDIR);
$realComponentDirs = array();

// generate componentwise call graphs
foreach ($componentDirs as $componentDir) {
    if (is_dir($componentDir . '/src')) {
        $componentName = basename($componentDir);
        echo $componentName, "\n";
        
        // start generation
        $phpcg->parse(array($componentDir . '/src'), true);

        // output result
        $phpcg->save("$targetDir/$componentName.png");

        // initiallize a new graph
        $driver->reset();

        // collect directories for an overall big picture
        $realComponentDirs[] = $componentDir;
    }
}

//removing Archive component since it produces an error
$realComponentDirs = array_diff($realComponentDirs, array('Archive'));

// generate an overall big picture (time consuming)
//$phpcg->parse($realComponentDirs, true);
//$phpcg->save("$targetDir/ezcomponents.png");
?>
