<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

/**
 * Require the test cases
 */
require_once 'extended_reflection_test.php';
require_once 'function_test.php';
require_once 'parameter_test.php';
require_once 'class_test.php';
require_once 'method_test.php';
require_once 'property_test.php';
require_once 'extension_test.php';
require_once 'type_factory_test.php';
require_once 'type_mapper_test.php';
require_once 'parser_test.php';
require_once 'tag_factory_test.php';

/** Test Subjects */
require_once 'test_classes/webservice.php';
require_once 'test_classes/methods.php';
require_once 'test_classes/methods2.php';
require_once 'test_classes/functions.php';

/** Helper Lib */
require_once 'test_helper.php';

/**
 * @package Reflection
 * @subpackage Tests
 */
class ezcReflectionSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName("Reflection");

        $this->addTest( ezcReflectionDocTagFactoryTest::suite() );
        $this->addTest( ezcReflectionDocParserTest::suite() );

        $this->addTest( ezcReflectionTest::suite() );
        $this->addTest( ezcReflectionFunctionTest::suite() );
        $this->addTest( ezcReflectionParameterTest::suite() );
        $this->addTest( ezcReflectionClassTest::suite() );
        $this->addTest( ezcReflectionMethodTest::suite() );
        $this->addTest( ezcReflectionPropertyTest::suite() );
        $this->addTest( ezcReflectionExtensionTest::suite() );
        $this->addTest( ezcReflectionTypeFactoryTest::suite() );
        $this->addTest( ezcReflectionTypeMapperTest::suite() );
    }

    public static function suite()
    {
        return new self();
    }
}
?>
