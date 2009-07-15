<?php
/**
 * Interface for call graph generation strategies.
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

/**
 * interface for call graph generation strategies
 */
interface CallgraphDriver {

    /**
     * Signifies that a method or function in a particular file is about to be analysed.
     *
     * @param integer $line - the line in the $file on which the current member starts 
     * @param string $file - the file being analysed
     * @param string $name - the name of the function under analysis
     * @param string $memberCode - the source code for just the method or function being analysed.
     * @return void
     */
    public function startFunction($line, $file, $name, $memberCode);

    /**
     * A call is being made from the currently analyzed startFunction to the $name
     *
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function addCall($line, $file, $name);

    /**
     * @return void
     */
    public function endFunction();

    /**
     * 
     * @return string
     */
    public function __toString();

    /**
     * NOT USED?
     *
     * @return void
     */
    public function reset();

}
