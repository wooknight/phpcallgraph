PHPCallGraph

    A tool to generate static call graphs for PHP source code.
    The graphs can be leveraged to gain a better understanding of
    large software systems or even to debunk design flaws in them.

    http://phpcallgraph.sourceforge.net/


    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.


Usage:
     Go to folder bin and run phpcallgraph.
     The generator requires at least PHP 5.2.

     In order to generate output formats other that 'txt' or 'cga'
     the Graphviz toolkit which can be downloaded at
     http://www.graphviz.org/ is required.

     bin/phpcallgraph [-f <string>] [-o <string>] [-r] [-d <string>] [-n] [-p] [-a <string>] [-v] [-g] [-h] [--] <string:sources> [<string:sources> ...]

Arguments:
    <string:sources>        Files and/or directories to analyze

Options:
    -f / --format           Set output format. Can be 'txt', 'array', 'cga' or
                            one of the formats supported by dot, e.g. png, svg,
                            pdf, ps, ...
                            (see http://graphviz.org/doc/info/output.html)
    -o / --outputfile       Output file
    -r / --recursive        Analyze directories recursive
    -d / --dotcommand       Set dot command
    -n / --noexternalcalls  Do not show calls to methods or functions which are
                            external to a class
    -p / --phpfunctions     Show calls to internal PHP functions
    -a / --autoload         Sets a PHP file with an autoload function which will
                            be included into the sandbox of the InstantSVC
                            CodeAnalyzer
    -v / --verbose          Verbose mode for text output format
    -g / --debug            Print debug information
                            (helpful if you get no output at all, since it
                            shows errors during code analysis)
    -h / --help             Display help

3D Graph Exploration:
    You can use the CGA framework available at http://cgs.hpi.uni-potsdam.de/trac/cga/
    to explore the graph with elaborate 3D visualization techniques.

    In order to generate input file for CGA set the format option to `cga', e.g.

        phpcallgraph -f cga -o testfiles.str test/testfiles/

Analysis of code in the global scope:
    Code in the global scope (outside any functions or methods) can be analyzed
    with the help of a little workaround: Such code can be manually wrapped in
    a dummy function called dummyFunctionForFile_filename_php() which will then
    be recognized by PHPCallGraph. Of course this is not very elegant but
    currently the only feasible way due to some conceptual restrictions
    resulting from the utilization of the InstantSVC CodeAnalyzer.

Examples:
    bin/phpcallgraph -n -f png -o PHPCallGraph.png src/PHPCallGraph.php
    bin/phpcallgraph -f png -o phpcallgraph-library.png src/drivers/ src/PHPCallGraph.php
    bin/phpcallgraph -r -f png -o phpcallgraph-src.png src/
    bin/phpcallgraph -r -f png -o phpcallgraph.png src/ lib/
    bin/phpcallgraph -r -p test/testfiles/
    bin/phpcallgraph -p -- test/testfiles/Foo.php test/testfiles/Bar.php
    bin/phpcallgraph -r -f array test/testfiles/ | php -r '$a = unserialize(file_get_contents("php://stdin")); var_export($a);'

Author:
    Falko Menge <fakko at users dot sourceforge dot net>
