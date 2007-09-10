<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package CodeAnalyzer
 * @subpackage Tests
 */

class ezcCodeAnalyzerTest extends ezcTestCase
{
	public function testSummarizeFile() {
		$result = iscCodeAnalyzer::summarizeFile(dirname(__FILE__).'/test_files/dependency/depclass.php');
		self::assertEquals(1, count($result['classes']));
		self::assertEquals(0, count($result['functions']));
        self::assertEquals(0, count($result['interfaces']));
        
        $result = iscCodeAnalyzer::summarizeFile(dirname(__FILE__).'/test_files/dependency/class.php');
		self::assertEquals(1, count($result['classes']));
		self::assertEquals(2, count($result['functions']));
        self::assertEquals(0, count($result['interfaces']));
	}

    public function testCollect_On_LoadDirFolder() {
        $ca = new iscCodeAnalyzer(dirname(__FILE__).'/test_files/load_dir');
        $ca->collect();
        $summary = $ca->getCodeSummary();

        self::assertEquals(4, count($summary['classes']));
        self::assertEquals(3, count($summary['functions']));
        self::assertEquals(0, count($summary['interfaces']));
        //self::markTestSkipped();
    }

    public function testSummarizeFunctions() {
        include_once(dirname(__FILE__).'/test_files/load_files/func.php');
        $summary = iscCodeAnalyzer::summarizeFunctions(
                                                 array('loadfile_testFunc1',
                                                       'loadfile_testFunc1a'));
        self::assertTrue(isset($summary['loadfile_testFunc1']));
        self::assertTrue(isset($summary['loadfile_testFunc1a']));
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( __CLASS__ );
    }
}
?>