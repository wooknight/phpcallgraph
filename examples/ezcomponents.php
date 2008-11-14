<?php
// change this path to your eZ Components SVN working copy
// also change it in ezcomponents-autoload.php
$ezcDir = '../../../../instantsvc/ezc-svn/trunk';

// set up include path
set_include_path(
    realpath(dirname(__FILE__) . '/../src') . PATH_SEPARATOR
    . realpath(dirname(__FILE__) . '/../lib/ezcomponents-instantsvc/components') . PATH_SEPARATOR
    . get_include_path() . PATH_SEPARATOR
    . realpath(dirname(__FILE__) . '/../lib/pear')
);

// configure eZ Components autoloader
// required because of phpCallGraph leveraging InstantSVC CodeAnalyzer and Extended Reflection API
require_once 'autoload-ezcomponents.php';

// include phpCallGraph library
require_once 'PHPCallGraph.php';

// configure output driver
require_once 'drivers/GraphVizDriver.php';
$driver = new GraphVizDriver('png');

// configure generator
$phpcg = new PHPCallGraph($driver);
$phpcg->setDebug(true);

// set a PHP file conaining an __autoload function which will be included
// in the sandbox of the InstantSVC CodeAnalyzer
$phpcg->setAutoloadFile('ezcomponents-autoload.php'); // will be found since the current include path is propagated into the sandbox

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

        //TODO: output SVGg and reder PNG with Inkscape
        //inkscape --without-gui --export-background=#ffffff --export-area-drawing --export-dpi=72 --export-png=a.png phpdoc-full.dot.svg
        // for thumbnails: --export-width=980

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
