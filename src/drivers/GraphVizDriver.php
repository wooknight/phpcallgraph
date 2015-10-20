<?php
/**
 * Implementation of a call graph generation strategy wich renders a graph with
 * dot.
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

// reduce warnings because of PEAR dependency
error_reporting(E_ALL ^ E_NOTICE);

require_once 'CallgraphDriver.php';
require_once 'Image/GraphViz.php';

/**
 * Implementation of a call graph generation strategy wich renders a graph with
 * dot.
 */
class GraphVizDriver implements CallgraphDriver {

    protected $outputFormat;
    protected $dotCommand;
    protected $useColor = true;
    protected $graph;
    protected $currentCaller = '';
    protected $internalFunctions;

    /**
     * @return CallgraphDriver
     */
    public function __construct($outputFormat = 'png', $dotCommand = 'dot') {
        $this->initializeNewGraph();
        $this->setDotCommand($dotCommand);
        $this->setOutputFormat($outputFormat);
        $functions = get_defined_functions();
        $this->internalFunctions = $functions['internal'];
        
    }

    /**
     * @return void
     */
    public function reset() {
        $this->initializeNewGraph();
    }

    /**
     * @return void
     */
    protected function initializeNewGraph() {
        $this->graph = new Image_GraphViz(
            true,
            array(
                'fontname'  => 'Verdana',
                'fontsize'  => 12.0,
                //'fontcolor' => 'gray5',
                'rankdir' => 'LR', // left-to-right
            )
        );
        $this->graph->dotCommand = $this->dotCommand;
    }

    /**
     * Sets path to GraphViz/dot command
     * @param string $dotCommand Path to GraphViz/dot command
     * @return void
     */
    public function setDotCommand($dotCommand = 'dot') {
        $this->dotCommand = $dotCommand;
        $this->graph->dotCommand = $dotCommand;
    }

    /**
     * Sets output format
     * @param string $outputFormat One of the output formats supported by GraphViz/dot
     * @return void
     */
    public function setOutputFormat($outputFormat = 'png') {
        $this->outputFormat = $outputFormat;
    }

    /**
     * Enables or disables the use of color
     * @param boolean $boolean True if color should be used
     * @return void
     */
    public function setUseColor($boolean = true) {
        $this->useColor = $boolean;
    }

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function startFunction($line, $file, $name, $memberCode) {
        $this->addNode($name);
        $this->currentCaller = $name;
    }

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function addCall($line, $file, $name) {
        $this->addNode($name);
        $this->graph->addEdge(array($this->currentCaller => $name));
    }

    /**
     * @return void
     */
    protected function addNode($name) {
        $nameParts = explode('::', $name);
        $cluster = 'default';
        $label = $name;
        $color = 'lavender'; //lightblue2, lightsteelblue2, azure2, slategray2
        if (count($nameParts) == 2) { // method call
            if (empty($nameParts[0])) {
                $cluster = 'class is unknown';
            } else {
                $cluster = $nameParts[0];
            }
            // obtain method name
            $label = $nameParts[1];
        }
        // remove parameter list
        $label = substr($label, 0, strpos($label, '('));

        if (count($nameParts) == 1) { // function call
            if (in_array($label, $this->internalFunctions)) { // call to internal function
                $cluster = 'internal PHP functions';
            }
        }
        $this->graph->addNode(
            $name,
            array(
                'fontname'  => 'Verdana',
                'fontsize'  => 12.0,
                //'fontcolor' => 'gray5',
                'label' => $label,
                //'style' => 'rounded' . ($this->useColor ? ',filled' : ''), // produces errors in rendering
                'style' => ($this->useColor ? 'filled' : 'rounded'),
                'color' => ($this->useColor ? $color : 'black'),
                'shape' => ($this->useColor ? 'ellipse' : 'rectangle'),
                ),
            $cluster
            );
        //*
        $this->graph->addCluster(
            $cluster,
            $cluster,
            array(
//                'style'   => ($this->useColor ? 'filled' : ''),
                'color'   => 'gray20',
//                'bgcolor' => '',
                )
            );
        //*/
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
        return $this->graph->fetch($this->outputFormat);
    }
}
?>
