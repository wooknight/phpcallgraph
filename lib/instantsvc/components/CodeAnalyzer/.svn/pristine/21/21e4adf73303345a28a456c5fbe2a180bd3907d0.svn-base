<?php
//***************************************************************************
//***************************************************************************
//**                                                                       **
//** iscCodeAnalyzerClassLoader                                            **
//**                                                                       **
//** @package    CodeAnalyzer                                              **
//** @author     Stefan Marr <mail@stefan-marr.de>                         **
//** @copyright  2006 InstantSVC Team                                      **
//** @license    www.apache.org/licenses/LICENSE-2.0   Apache License 2.0  **
//**                                                                       **
//***************************************************************************
//***************************************************************************

//***** iscCodeAnalyzerClassLoader ******************************************
/**
 * @package    CodeAnalyzer
 * @author     Stefan Marr <mail@stefan-marr.de>
 * @copyright  2006 InstantSVC Team
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */
class iscCodeAnalyzerClassLoader {
	
	private function __construct() {}

    /**
     * Load php-files in the given directory recursivly
     * @param string $path
     */
    public static function loadDir($path) {
        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while (($file = readdir($dir)) !== false) {
                    if ($file != '..' && $file != '.' &&
                        $file != '.svn' && $file != 'CVS') {
                        if (is_dir($path.'/'.$file)) {
                            self::loadDir($path.'/'.$file);
                        }
                        else {
                            self::loadFile($path.'/'.$file);
                        }
                    }
                }
                closedir($dir);
            }
        }
    }

    /**
     * Includes a file if it seams to be a php file.
     * All files without parsing errors are included.
     * @param string $file
     */
    public static function loadFile($file) {
        if ($file != '' and file_exists( $file )) {

            try {
                //TODO: Test whether it is neccessary to use start /B on win32 if
                //called from apache/none-cli to prevent console popup's
                //if (ezcSystemInfo::getInstance()->osType == 'win32') {
                //}
                
                exec( 'php -l ' . escapeshellarg( $file ), $output, $return );
	
	                //if no parsing error occured, file can be included
                if ( $return == 0 ) {
                	ob_start();
                    include_once( $file );
                    ob_end_clean();
                }
            }
            catch(Exception $e) {
                //nothing to do with exceptions here
                unset($e);
            }
        }
    }
}

?>