<?php
/**
 * Implementation of a call graph generation strategy wich output the calls as text.
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
 * implementation of a call graph generation strategy wich output the calls as text
 */

class ArrayDriver implements CallgraphDriver {

    /**
     * @var string
     */
    protected $func = '';

    /**
     * @var boolean
     */
    protected $verbose;

    /**
     * @var array
     */
    protected $stack = array();

    /**
     * @param boolean $verbose
     * @return CallgraphDriver
     */
    public function __construct($verbose = false) {
        $this->verbose = $verbose;
    }

    /**
     * @param integer $line Line.
     * @param string  $file File name.
     * @param string  $name Name of the function/method.
     * @return void
     */
    public function startFunction($line, $file, $name) {
        $this->stack[$name] = array();
        $this->func         = $name;

        // $this->text.= $name;
        if ($this->verbose) {
            $this->stack[$name]['verbose'] = array(
                "file" => $file,
                "line" => $line
            );
        }
        // $this->text.= "\n";
    }

    /**
     * @param integer $line Line.
     * @param string  $file File name.
     * @param string  $name Name of the call.
     * @return void
     */
    public function addCall($line, $file, $name) {
        if (!isset($this->stack[$this->func]['call'])) {
            $this->stack[$this->func]['call'] = array();
        }
        array_push($this->stack[$this->func]['call'], $name);

        if ($this->verbose) {
            // $this->text.= " -- called on line $line and defined in $file";
        }
        // $this->text.= "\n";
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
        return serialize($this->stack);
    }

    /**
     * @return void
     */
    public function reset() {
        $this->stack = array();
        $this->func  = '';
    }
}
