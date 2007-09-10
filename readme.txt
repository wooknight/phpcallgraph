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
     The generator requires PHP5.

     bin/phpcallgraph [-f <string>] [-o <string>] [-r] [-d <string>] [-n] [-p] [-v] [-h] [--] <string:sources> [<string:sources> ...]

Arguments:
    <string:sources>        files and/or directories to analyze

Options:
    -f / --format           set output format; can be 'txt', 'cga' or one of the
                            formats supported by dot
    -o / --outputfile       output file
    -r / --recursive        analyze directories recursive
    -d / --dotcommand       set dot command
    -n / --noexternalcalls  do not show calls to methods or functions which are
                            external to a class
    -p / --phpfunctions     show calls to internal PHP functions
    -v / --verbose          verbose mode for text output format
    -h / --help             display help

3D Graph Exploration:
    You can use the CGA framework available at http://cgs.hpi.uni-potsdam.de/trac/cga/
    to explore the graph with elaborate 3D visualization techniques.

    In order to generate input file for CGA set the format option to `cga', e.g.

        phpcallgraph -f cga -o testfiles.str test/testfiles/

Examples:
    bin/phpcallgraph -n -f png -o PHPCallGraph.png src/PHPCallGraph.php
    bin/phpcallgraph -f png -o phpcallgraph-library.png src/drivers/ src/PHPCallGraph.php
    bin/phpcallgraph -r -f png -o phpcallgraph-src.png src/
    bin/phpcallgraph -r -f png -o phpcallgraph.png src/ lib/
    bin/phpcallgraph -r -p test/testfiles/
    bin/phpcallgraph -p -- test/testfiles/Foo.php test/testfiles/Bar.php

Author:
    Falko Menge <fakko at users dot sourceforge dot net>
