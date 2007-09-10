<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package CodeAnalyzer
 * @subpackage Tests
 */

/**
 * Require the test cases
 */
require_once 'file_details_test.php';
require_once 'code_analyzer_test.php';
require_once 'class_loader_test.php';

/**
 * @package Reflection
 * @subpackage Tests
 */
class ezcCodeAnalyzerSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName('CodeAnalyzer');

        $this->addTest( ezcCodeAnalyzerFileDetailsTest::suite() );
        $this->addTest( ezcCodeAnalyzerClassLoaderTest::suite() );
        $this->addTest( ezcCodeAnalyzerTest::suite() );
    }

    public static function suite()
    {
        return new self();
    }
}
?>
