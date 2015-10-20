<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package CodeAnalyzer
 * @subpackage Tests
 */

class ezcCodeAnalyzerFileDetailsTest extends ezcTestCase
{
    public function testWithPhpFile() {
        $file = new iscCodeAnalyzerFileDetails(dirname(__FILE__).
                                               '/test_files/class.php');
        self::assertEquals( realpath(dirname(__FILE__).'/test_files/class.php'),
                            $file->fileName );
        self::assertEquals( 76, $file->fileSize );
        self::assertEquals( 5, $file->linesOfCode );
        self::assertEquals( 'application/x-httpd-php', $file->mimeType );
    }

    public function testWithTextFile() {
        $file = new iscCodeAnalyzerFileDetails(dirname(__FILE__).
                                               '/test_files/test.txt');
        self::assertEquals( realpath(dirname(__FILE__).'/test_files/test.txt'),
                            $file->fileName );
        self::assertEquals( 79, $file->fileSize );
        self::assertEquals( 5, $file->linesOfCode );
        self::assertEquals( 'text/plain', $file->mimeType );
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( __CLASS__ );
    }
}
?>
