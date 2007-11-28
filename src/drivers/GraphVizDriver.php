<?php
/**
 * Implementation of a call graph generation strategy wich renders a graph with dot.
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
require_once 'Image/GraphViz.php';

/**
 * implementation of a call graph generation strategy wich renders a graph with dot
 */
class GraphVizDriver implements CallgraphDriver {

    protected $outputFormat;
    protected $useColor = true;
    protected $graph;
    protected $currentCaller = '';
    protected $internalFunctions;

    /**
     * @return CallgraphDriver
     */
    public function __construct($dotCommand = 'dot', $outputFormat = 'png') {
        $this->setOutputFormat($outputFormat);
        $this->graph = new Image_GraphViz();
        $this->graph->dotCommand = $dotCommand;
        $functions = get_defined_functions();
        $this->internalFunctions = $functions['internal'];
    }

    public function setDotCommand($dotCommand = 'dot') {
        $this->graph->dotCommand = $dotCommand;
    }

    public function setOutputFormat($outputFormat = 'png') {
        $this->outputFormat = $outputFormat;
    }

    public function setUseColor($boolean = true) {
        $this->useColor = $boolean;
    }

    /**
     * @param integer $line
     * @param string $file
     * @param string $name
     * @return void
     */
    public function startFunction($line, $file, $name) {
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
        $color = 'lightblue2';
        if (count($nameParts) == 2) {
            if (empty($nameParts[0])) {
                $cluster = 'class is unknown';
            } else {
                $cluster = $nameParts[0];
            }
            $label = $nameParts[1];
        }
        $label = substr($label, 0, strpos($label, '(')); 
        if (in_array($label, $this->internalFunctions)) {
            $cluster = 'internal PHP functions';
        }
        $this->graph->addNode(
            $name,
            array(
                'label' => $label,
                'style' => ($this->useColor ? 'filled' : ''),
                'color' => $color,
                ),
            $cluster
            );
        //*
        $this->graph->addCluster(
            $cluster,
            $cluster,
            array(
//                'style' => ($this->useColor ? 'filled' : ''),
//                'color' => 'slateblue',
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
