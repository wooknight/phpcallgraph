<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package CodeAnalyzer
 * @subpackage Tests
 */

class ezcCodeAnalyzerClassLoaderTest extends ezcTestCase
{
    public function testLoadDir() {
        $classes = get_declared_classes();
        $functions = get_defined_functions();
        $userFunctions = $functions['user'];
        iscCodeAnalyzerClassLoader::loadDir(dirname(__FILE__).'/test_files/load_dir');
        $newClasses = array_diff(get_declared_classes(), $classes);
        $functions = get_defined_functions();
        $newFunctions = array_diff($functions['user'], $userFunctions);

        $expectedClasses = array('FileDetailsTestClass2',
                                 'FileDetailsTestClass3',
                                 'FileDetailsTestClass4',
                                 'ClassLoaderTestClassHtml');
        $expectedFunctions = array('classloadertestfunct1', 'classloadertestfunct2',
                                   'classloadertestfunct3');

        self::expectedArray($expectedClasses, $newClasses);
        self::expectedArray($expectedFunctions, $newFunctions);
    }

    public function testLoadFile1() {
        $classes = get_declared_classes();
        $functions = get_defined_functions();
        $userFunctions = $functions['user'];
        iscCodeAnalyzerClassLoader::loadFile(dirname(__FILE__).'/test_files/load_files/class.inc');
        $newClasses = array_diff(get_declared_classes(), $classes);
        $functions = get_defined_functions();
        $newFunctions = array_diff($functions['user'], $userFunctions);

        $expectedClasses = array('LoadFileTestClass');
        $expectedFunctions = array();
        self::expectedArray($expectedClasses, $newClasses);
        self::expectedArray($expectedFunctions, $newFunctions);
    }

    public function testLoadFile2() {
        $classes = get_declared_classes();
        $functions = get_defined_functions();
        $userFunctions = $functions['user'];
        iscCodeAnalyzerClassLoader::loadFile(dirname(__FILE__).'/test_files/load_files/docu.txt');
        $newClasses = array_diff(get_declared_classes(), $classes);
        $functions = get_defined_functions();
        $newFunctions = array_diff($functions['user'], $userFunctions);

        self::assertEquals(0, count($newClasses));
        self::assertEquals(0, count($newFunctions));
    }

    public function testLoadFile3() {
        $classes = get_declared_classes();
        $functions = get_defined_functions();
        $userFunctions = $functions['user'];
        iscCodeAnalyzerClassLoader::loadFile(dirname(__FILE__).'/test_files/load_files/func.php');
        $newClasses = array_diff(get_declared_classes(), $classes);
        $functions = get_defined_functions();
        $newFunctions = array_diff($functions['user'], $userFunctions);

        $expectedClasses = array();
        $expectedFunctions = array('loadfile_testfunc1a', 'loadfile_testfunc1');
        self::expectedArray($expectedClasses, $newClasses);
        self::expectedArray($expectedFunctions, $newFunctions);
    }

    static public function expectedArray($expected, $current) {
        foreach ($current as $item) {
            self::assertContains($item, $expected);
            self::deleteFromArray($item, $expected);
        }
        self::assertEquals(0, count($expected));
    }

    /**
     * Helper method to delete a given value from an array
     *
     * @param mixed $needle
     * @param mixed $array
     */
    static public function deleteFromArray($needle, &$array) {
        foreach ($array as $key => $value) {
            if ($value == $needle) {
                unset($array[$key]);
                return;
            }
        }
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( __CLASS__ );
    }
}
?>