<?php
/**
 * Console front-end to use PHPCallGraph from the command line
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

// include PHPCallGraph library
require_once 'PHPCallGraph.php';

/**
 * console front-end to use PHPCallGraph from the command line
 *
 * This class requires eZ Components autoloader to be configured
 * because it uses ezcConsoleTools
 */
class PHPCallGraphCli {

    /**
     * @return PHPCallGraphCli
     */
    public function __construct() {
    }

    /**
     * starts the command line interface for PHPCallGraph
     * @return void
     */
    public function run() {
        $input = new ezcConsoleInput();
        $formatOption = $input->registerOption( 
            new ezcConsoleOption( 
                'f',
                'format',
                ezcConsoleInput::TYPE_STRING
            )
        );
        $formatOption->shorthelp = "set output format; can be 'txt', 'cga' or one of the formats supported by dot";

        $outputfileOption = $input->registerOption(
            new ezcConsoleOption( 
                'o',
                'outputfile',
                ezcConsoleInput::TYPE_STRING
            )
        );
        $outputfileOption->shorthelp = 'output file';

        $recursiveOption = $input->registerOption(
            new ezcConsoleOption( 
                'r',
                'recursive',
                ezcConsoleInput::TYPE_NONE
            )
        );
        $recursiveOption->shorthelp = 'analyze directories recursive';

        $dotcommandOption = $input->registerOption(
            new ezcConsoleOption( 
                'd',
                'dotcommand',
                ezcConsoleInput::TYPE_STRING
            )
        );
        $dotcommandOption->shorthelp = 'set dot command';

        $noexternalcallsOption = $input->registerOption(
            new ezcConsoleOption( 
                'n',
                'noexternalcalls',
                ezcConsoleInput::TYPE_NONE
            )
        );
        $noexternalcallsOption->shorthelp = 'do not show calls to methods or functions which are external to a class';

        $phpfunctionsOption = $input->registerOption(
            new ezcConsoleOption( 
                'p',
                'phpfunctions',
                ezcConsoleInput::TYPE_NONE
            )
        );
        $phpfunctionsOption->shorthelp = 'show calls to internal PHP functions';

        $verboseOption = $input->registerOption(
            new ezcConsoleOption( 
                'v',
                'verbose',
                ezcConsoleInput::TYPE_NONE
            )
        );
        $verboseOption->shorthelp = 'verbose mode for text output format';

        $helpOption = $input->registerOption( 
            new ezcConsoleOption( 
                'h',
                'help'
            )
        );
        $helpOption->isHelpOption = true; // if parameter is set, all options marked as mandatory may be skipped
        $helpOption->shorthelp = 'display help';

        $input->argumentDefinition = new ezcConsoleArguments();
        $input->argumentDefinition[0] = new ezcConsoleArgument(
                'sources',
                ezcConsoleInput::TYPE_STRING,
                'files and/or directories to analyze',
                '',
                true,
                true
                );

        try {
            $input->process();
        } catch (ezcConsoleOptionException $e) {
            die($e->getMessage() . "\nTry option -h to get a list of available options.\n");
        } catch (ezcConsoleArgumentMandatoryViolationException $e) {
            die($e->getMessage() . "\nTry option -h to get a list of available options.\n");
        }

        if ($helpOption->value === true) {
            echo $input->getHelpText(
                 "\nPHPCallGraph v" . PHPCallGraph::VERSION . "\n\n"
                 . "A tool to generate static call graphs for PHP source code.\n"
                 . "The graphs can be leveraged to gain a better understanding of\n"
                 . "large software systems or even to debunk design flaws in them."
            );
        } else {
            // configure output driver according to format option
            switch ($formatOption->value) {
                case 'cga':
                    require_once 'drivers/CgaStaticTraceDriver.php';
                    $driver = new CgaStaticTraceDriver();
                    break;
                case false:
                case 'txt':
                    require_once 'drivers/TextDriver.php';
                    $driver = new TextDriver($verboseOption->value);
                    break;
                default:
                    require_once 'drivers/GraphVizDriver.php';
                    $driver = new GraphVizDriver();
                    $driver->setOutputFormat($formatOption->value);
                    if ($dotcommandOption->value !== false) {
                        $driver->setDotCommand($dotcommandOption->value);
                    }
            }

            // configure generator
            $phpcg = new PHPCallGraph($driver);

            if ($noexternalcallsOption->value !== false) {
                $phpcg->setShowExternalCalls(false);        
            }

            if ($phpfunctionsOption->value !== false) {
                $phpcg->setShowInternalFunctions(true);        
            }

            // start generation
            $phpcg->parse($input->getArguments(), $recursiveOption->value);

            // output result
            if ($outputfileOption->value === false) {
                echo $phpcg;
            } else {
                $phpcg->save($outputfileOption->value);
            }
        }
    }
}
?>
