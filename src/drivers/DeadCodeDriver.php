<?php
/**
 * Implementation of a call graph generation strategy wich outputs methods and
 * functions that are never called as plain text.
 *
 * PHP version 5
 *
 * This file is part of phpCallGraph.
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
 * @copyright  2007-2009 Falko Menge
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */

require_once 'CallgraphDriver.php';
require_once 'static-reflection/function.php';

/**
 * Implementation of a call graph generation strategy wich outputs methods and
 * functions that are never called as plain text.
 */

class DeadCodeDriver implements CallgraphDriver {

    /**
     * @var staticReflectionFunction[] List of defined functions
     */
    protected $definedFunctions = array();

    /**
     * @var staticReflectionFunction[] List of called functions
     */
    protected $calledFunctions = array();

    /**
     * @var boolean
     */
    protected $verbose;

    /**
     * @param boolean $verbose
     * @return CallgraphDriver
     */
    public function __construct($verbose = false) {
        $this->verbose = $verbose;
    }

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function startFunction($line, $file, $name) {
        $function = new staticReflectionFunction($name, $file);
        $function->startLine = $line;
        $this->definedFunctions[] = $function;
    }

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function addCall($line, $file, $name) {
        $function = new staticReflectionFunction($name, $file);
        $this->calledFunctions[] = $function;
    }

    /**
     * @return void
     */
    public function endFunction() {
    }

    /**
     * @return string
     */
    public function __toString() {
        $output = '';
        $unusedFunctions = array_diff($this->definedFunctions, $this->calledFunctions);
        foreach ($unusedFunctions as $function) {
            $output .= $function->getName();
            if ($this->verbose) {
                $output .= " defined in {$function->fileName} on line {$function->startLine}";
            }
            $output .= "\n";
        }
        return $output;
    }

    /**
     * @return void
     */
    public function reset() {
        $this->functions = array();
    }

}
