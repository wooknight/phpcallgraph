<?php
error_reporting(E_ALL | ~E_STRICT);
ini_set('display_errors',true);

require_once 'PEAR/PackageFileManager2.php';
require_once 'PEAR/PackageFileManager/File.php';
require_once 'PEAR/Task/Postinstallscript/rw.php';
require_once 'PEAR/Config.php';
require_once 'PEAR/Frontend.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$channel = '__uri'; // FIXME
$version = '0.7.0'; // FIXME
$name    = 'phpCallGraph';
$summary = 'phpCallGraph';

$ezc      = array('Base', 'ConsoleTools');
$ezc_fake = array('Reflection', 'CodeAnalyzer');
$pear     = array('Image_GraphViz');

$dir_dest = dirname(__FILE__);

$files      = rglob('*', 0, $dir_dest . '/src/');
$install_as = array();
foreach ($files as $file) {
    if (is_dir($file)) {
        continue;
    }
    $key = str_replace($dir_dest . '/', '', $file);
    $val = str_replace($dir_dest . '/src/', '', $file);
    $install_as[$key] = $val;
}
//var_dump($install_as); exit;

$pfm = new PEAR_PackageFileManager2();
$pfm->setOptions(array(
    'packagedirectory'  => $dir_dest,
    'baseinstalldir'    => 'phpCallGraph',
    'filelistgenerator' => 'file',
    'ignore' => array(
        $dir_dest . '/package.xml',
        $dir_dest . '/package.xml.php',
        $dir_dest . '/lib',
        $dir_dest . '/.project',
        $dir_dest . '/*.tgz',
        $dir_dest . '/lib/*',
        $dir_dest . '/setup-env.sh',
        $dir_dest . '/examples/ezcomponents*',
        $dir_dest . '/examples/PHPCallGraph-640x229.png',
        $dir_dest . '/examples/testfiles-640x206.png',
        '*.SVN/*',
        '.cache/*',
    ),
    'simpleoutput' => true,
    'include'      => array(
        $dir_dest . '/bin/',
        $dir_dest . '/src/',
        $dir_dest . '/test/',
        $dir_dest . '/examples/',
        $dir_dest . '/readme.txt',
        $dir_dest . '/license.txt',
    ),
    'dir_roles' => array(
        'src'      => 'php',
        'bin'      => 'script',
        'lib'      => 'php',
        'test'     => 'test',
        'examples' => 'doc',
        'docs'     => 'doc',
    ),
    'exceptions' => array(
        'readme.txt'  => 'doc',
        'license.txt' => 'doc',
    )
));

$pfm->setPackage($name);
$pfm->setPackageType('php');
$pfm->setSummary($summary);
$pfm->setDescription('This is some description.');
$pfm->setChannel($channel);
$pfm->setAPIStability('stable');
$pfm->setReleaseStability('stable');
$pfm->setAPIVersion($version); // FIXME 
$pfm->setReleaseVersion($version);
$pfm->setNotes($summary);

// INSTALLAS: rewrite src
foreach ($install_as as $key => $val) {
    $pfm->addInstallAs($key, $val);
}

$pfm->addMaintainer('lead', 'falco', 'Falko Menge', 'fakko at users dot sourceforge dot net');

$pfm->setLicense('GPLv3', 'http://www.gpl.org');

$pfm->clearDeps();

$pfm->setPhpDep('5.2.1');
$pfm->setPearinstallerDep('1.7.1');

// DEPS: ezComponents
foreach ($ezc as $ez) {
    $pfm->addPackageDepWithChannel('required', $ez, 'components.ez.no');
}

// DEPS: fake ezComponents
$pfm->addPackageDepWithUri('required', 'ezcReflection', 'http://tmp.cweiske.de/ezcReflection-0.7.0');
$pfm->addPackageDepWithUri('required', 'CodeAnalyzer', 'http://tmp.cweiske.de/CodeAnalyzer-0.7.0');

// DEPS: PEAR
foreach ($pear as $pkg) {
    $pfm->addPackageDepWithChannel('required', $pkg, 'pear.php.net');
}

$pfm->generateContents();

// bin/ - HA! - VERARSCHT!!

$prop = array('role' => 'script', 'baseinstalldir' => '');

$pfm->addFile('bin', 'phpcallgraph.bat', $prop);
$pfm->addFile('bin', 'phpcallgraph', $prop);

//$pfm->addIgnoreToRelease('bin/phpcallgraph');
//$pfm->addIgnoreToRelease('bin/phpcallgraph.bat');

// OS: WINDOWS
//$pfm->addRelease(); // set up a release section
//$pfm->setOSInstallCondition('windows');
//$pfm->addInstallAs('bin/phpcallgraph.bat', 'phpcallgraph.bat');
//$pfm->addIgnoreToRelease('bin/phpcallgraph');

// OS: *
//$pfm->addRelease(); // set up a release section
//$pfm->setOSInstallCondition('*');
//$pfm->addInstallAs('bin/phpcallgraph', 'phpcg');
//$pfm->addIgnoreToRelease('bin/phpcallgraph.bat');

$pfm->writePackageFile();
//$pfm->debugPackageFile();

/**
 * FUNCTIONS
 */

function rglob($pattern='*', $flags = 0, $path='')
{
    $paths = glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files = glob($path.$pattern, $flags);
    foreach ($paths as $path) {
        $files=array_merge($files,rglob($pattern, $flags, $path));
    }
    return $files;
}
