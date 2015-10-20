<?php
error_reporting(E_ALL);
/*
require_once 'ezc/Base/base.php';
function __autoload( $className ) { ezcBase::autoload( $className ); }

include(dirname(__FILE__).'/../../../../src/code_analyzer.php');
*/

eval("include('../depclass-relative.php');");
 var_dump(get_declared_classes());

//$result = iscCodeAnalyzer::summarizeFile('../depclass-relative.php');
//var_dump($result);

?>