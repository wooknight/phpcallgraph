<?php
error_reporting(E_ALL | E_STRICT);

require_once 'ezc/Base/base.php';
//ezcBase::addClassRepository('ezc/UnitTest');
function __autoload( $className ) { ezcBase::autoload( $className ); }

//require_once '../../UnitTest/src/test/runner.php';
//require_once '../../UnitTest/src/test/case.php';
//require_once '../../UnitTest/src/test/suite.php';

require_once '../src/reflection.php';
require_once '../src/class.php';
require_once '../src/method.php';
require_once '../src/property.php';
require_once '../src/parameter.php';
require_once '../src/extension.php';
require_once '../src/function.php';
require_once '../src/interfaces/parser.php';
require_once '../src/phpdoc/parser.php';
require_once '../src/phpdoc/tag_factory.php';
require_once '../src/phpdoc/tag.php';
require_once '../src/type_mapper.php';
require_once '../src/interfaces/type.php';
require_once '../src/interfaces/type_factory.php';
require_once '../src/types/class_type.php';
require_once '../src/types/abstract_type.php';
require_once '../src/types/array_type.php';
require_once '../src/types/primitive_type.php';
require_once '../src/type_factory.php';


require_once '../src/tags/return.php';
require_once '../src/tags/param.php';
require_once '../src/tags/var.php';
require_once '../src/tags/rest_in.php';
require_once '../src/tags/rest_out.php';
require_once '../src/tags/rest_method.php';
require_once '../src/tags/webmethod.php';
require_once '../src/tags/webservice.php';



include('suite.php');
?>