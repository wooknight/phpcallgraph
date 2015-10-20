<?php
//require_once 'ezc/Base/base.php';
set_include_path( '../../' . PATH_SEPARATOR . ini_get("include_path"));
require_once 'Base/src/base.php';
function __autoload($className) { ezcBase::autoload( $className ); }

// analyze a directory tree
$ca = new iscCodeAnalyzer('../src');
$ca->collect();
$summary = $ca->getCodeSummary();
$stats = $ca->getStats();

// analyze files
$ca = new iscCodeAnalyzer('');
$ca->inspectFiles(array('../src/class_loader.php', '../src/file_details.php'));
$fileinfos = $ca->getCodeSummary();

echo "<?php\n\$codeSummary = " . var_export($summary, true)
     . "\n\$stats = " . var_export($stats, true)
     . "\n\$fileinfos = " . var_export($fileinfos, true)
     . "\n?>";
?>
