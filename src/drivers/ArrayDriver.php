<?php
/**
 * Implementation of a call graph generation strategy wich outputs the calls as
 * an associative array.
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
 * @author     Till Klampaeckel <till at php dot net>
 * @copyright  2007-2009 Falko Menge
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */

/**
 * CallGraphDriver
 */
require_once 'CallgraphDriver.php';

/**
 * Implementation of a call graph generation strategy wich outputs the calls as
 * an associative array.
 */
class ArrayDriver implements CallgraphDriver {

    /**
     * @var string The current function/method.
     * @see self::startFunction()
     */
    protected $func = '';

    /**
     * @var boolean More info, yes/no.
     */
    protected $verbose;

    /**
     * @var array Collects info on the current code.
     */
    protected $stack = array();

    /**
     * CTR
     *
     * @param boolean $verbose
     *
     * @return CallgraphDriver
     * @uses   self::$verbose
     */
    public function __construct($verbose = false) {
        if (is_bool($verbose)) {
            $this->verbose = $verbose;
        }
    }

    /**
     * Called when a new function is discovered. Sets {@link self::$func}.
     *
     * @param integer $line Line.
     * @param string  $file File name.
     * @param string  $name Name of the function/method.
     *
     * @return void
     * @uses   self::$stack
     * @uses   self::$func
     * @uses   self::$verbose
     */
    public function startFunction($line, $file, $name) {
        $this->stack[$name] = array();
        $this->func         = $name;

        if ($this->verbose) {
            $this->stack[$name]['verbose'] = array(
                "file" => $file,
                "line" => $line
            );
        }
    }

    /**
     * Adds a call from within the current {@link self::$func}.
     *
     * @param integer $line Line.
     * @param string  $file File name.
     * @param string  $name Name of the call.
     *
     * @return void
     * @uses   self::$stack
     * @uses   self::$func
     * @uses   self::$verbose
     * @todo   Implement verbose.
     */
    public function addCall($line, $file, $name) {
        if (!isset($this->stack[$this->func]['call'])) {
            $this->stack[$this->func]['call'] = array();
        }
        array_push($this->stack[$this->func]['call'], $name);

        if ($this->verbose) {
            // FIXME: verbose for calls
            // $this->text.= " -- called on line $line and defined in $file";
        }
    }

    /**
     * @return void
     */
    public function endFunction() {
    }

    /**
     * Returns {@link self::$stack} as a string, serialized.
     *
     * @return string
     * @uses   self::$stack
     * @uses   serialize()
     */
    public function __toString() {
        return serialize($this->stack);
    }

    /**
     * Return the stack
     *
     * @return array
     * @uses   self::$stack
     */
    public function getArray() {
        return $this->stack;
    }

    /**
     * Resets all
     *
     * @return void
     */
    public function reset() {
        $this->stack = array();
        $this->func  = '';
    }
}
