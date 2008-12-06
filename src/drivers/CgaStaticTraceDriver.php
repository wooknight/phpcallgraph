<?php
/**
 * Call graph generation strategy wich generates a CGA static trace.
 *
 * PHP version 5
 *
 * This file is part of PHPCallGraph.
 *
 * PHPCallGraph is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPCallGraph is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    PHPCallGraph
 * @author     Falko Menge <fakko at users dot sourceforge dot net>
 * @copyright  2007 Falko Menge
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */

require_once 'CallgraphDriver.php';

/**
 * implementation of a call graph generation strategy wich generates a CGA static trace
 */
class CgaStaticTraceDriver implements CallgraphDriver {

    /**
     * @var string
     */
    protected $xml = '';

    /**
     * @return CallgraphDriver
     */
    public function __construct() {
        $this->xml = '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n"
            . '<callgraph xmlns="http://www.hpi.uni-potsdam.de/cgs/cga/staticcallgraph">' . "\n";
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->xml . '</callgraph>';
    }

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function startFunction($line, $file, $name) {
        $this->xml .= "\t<fn>\n"
            . "\t\t<src line=\"$line\" file=\"$file\" name=\"$name\" />\n";
    }

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function addCall($line, $file, $name) {
        // the line of a call is no longer part of the language specification
        $this->xml .= "\t\t<call>\n"
            . "\t\t\t<src file=\"$file\" name=\"$name\" />\n"
            . "\t\t</call>\n";
    }

    /**
     * @return void
     */
    public function endFunction() {
        $this->xml .= "\t</fn>\n";
    }

    /**
     * @return void
     */
    public function reset() {
        $this->xml = '';
    }

}
?>
