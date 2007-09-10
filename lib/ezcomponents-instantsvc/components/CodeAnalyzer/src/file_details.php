<?php
//***************************************************************************
//***************************************************************************
//**                                                                       **
//** iscCodeAnalyzerFileDetails                                            **
//**                                                                       **
//** @package    CodeAnalyzer                                              **
//** @author     Stefan Marr <mail@stefan-marr.de>                         **
//** @copyright  2006 InstantSVC Team                                      **
//** @license    www.apache.org/licenses/LICENSE-2.0   Apache License 2.0  **
//**                                                                       **
//***************************************************************************
//***************************************************************************

//***** iscCodeAnalyzerFileDetails ******************************************
/**
 * Struct containing details about a file.
 * Guessing MimeType, counting lines of code, and retrieving file size are done.
 *
 * @package    CodeAnalyzer
 * @author     Stefan Marr <mail@stefan-marr.de>
 * @copyright  2006 InstantSVC Team
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */
class iscCodeAnalyzerFileDetails extends ezcBaseStruct {

    /**
     * @var string
     */
    public $mimeType;

    /**
     * @var int
     */
    public $linesOfCode = 0;

    /**
     * @var int
     */
    public $fileSize = 0;

    /**
     * @var string
     */
    public $fileName = '';

    /**
     * @var int
     */
    public $countClasses = 0;

    /**
     * @var int
     */
    public $countFunctions = 0;

    /**
     * @var int
     */
    public $countInterfaces = 0;

    /**
     * @var MimeHandler
     */
    private static $mimeHandler = null;

    /**
     * @var string
     */
    private static $locMimes = null;

    /**
     * @var array(string => string)
     */
    private static $mimes = null;

    /**
     * Handle for mime database
     * @var resource
     */
    private static $finfo;

    /**
     * Init class variables and detects available mime guess method
     */
    private static function initClass() {
        if (function_exists('finfo_file') && function_exists('finfo_open')) {
            self::$mimeHandler = 1;
            self::$finfo = finfo_open(FILEINFO_MIME);
        }
        elseif (function_exists('mime_content_type')) {
            self::$mimeHandler = 2;
        }
        else {
            //map file extensions to mime type
            $php = array('php', 'php3', 'php4', 'php5', 'inc');
            foreach ($php as $key) {
                self::$mimes[$key] = 'application/x-httpd-php';
            }

            $image = array('jpg', 'bmp', 'gif', 'png', 'tiff', 'tif');
            foreach ($image as $key) {
                self::$mimes[$key] = 'image';
            }

            self::$mimes['txt'] = 'text/plain';

            //list file types for count lines of count
            self::$locMimes[] = 'application/x-httpd-php';
            self::$locMimes[] = 'application/x-php';
            self::$locMimes[] = 'application/x-javascript';
            self::$mimeHandler = 3;
        }
    }

    //=======================================================================
    /**
     * @param string $file
     */
    public function __construct($file = '') {
        if (self::$mimeHandler == null) {
            self::initClass();
        }

        $this->fileName = realpath($file);
        if ($file != '' and file_exists($file)) {
            $this->mimeType = $this->guessMimeType($file);

            if ($this->shouldCountLines($this->mimeType)) {
                $this->linesOfCode = count(file($file));
            }
            else {
                $this->linesOfCode = null;
            }
            $this->fileSize = filesize($file);
        }
    }

    //=======================================================================
    /**
     * Guess the mime type using the file extension
     * @param string $file
     * @return boolean
     */
    protected function guessMimeType($file) {
        switch (self::$mimeHandler) {
        	case 1:
                return finfo_file(self::$finfo, $file);
        	case 2:
        		return mime_content_type($file);
        	default:
        		break;
        }

        $parts = explode('.', $file);
        if (isset(self::$mimes[$parts[count($parts)-1]])) {
            return self::$mimes[$parts[count($parts)-1]];
        }

        return 'unknown/mimetype';
    }

    /**
     * Use mime type to decide wheter the lines of code are counable
     *
     * @param string $mime
     * @return boolean
     */
    protected function shouldCountLines($mime) {
        if (in_array($mime, self::$locMimes) or strpos($mime, 'text') !== false) {
            return true;
        }
        return false;
    }
}
?>