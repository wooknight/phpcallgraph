<?php
error_reporting(E_ALL);
/*$savetyProlog = 'throw new Exception(\'Abort User Code with Exception\', 0);?>';
$savetyProlog = 'return;?>';
//try {
	eval($savetyProlog . file_get_contents('class.php'));
//}
//catch (Exception $e) {
//	unset($e);
//}

$test = new FileDetailsTestClass();
$test->doSomething();

$class = new ReflectionClass('FileDetailsTestClass');
echo $class->getFileName();
if (strpos($class->getFileName(), 'eval()') !== false) {
	echo '  ok';
}*/

require_once 'ezc/Base/base.php';
function __autoload( $className ) { ezcBase::autoload( $className ); }

include(dirname(__FILE__).'/../../src/code_analyzer.php');

//$result = iscCodeAnalyzer::summarizeFile('class2.php');
$result = iscCodeAnalyzer::summarizeFile('dependency/class.php');
$result = iscCodeAnalyzer::summarizeFile('dependency/depclass.php');
var_dump($result);

?>