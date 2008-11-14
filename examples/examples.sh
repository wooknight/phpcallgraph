#!/bin/sh
../bin/phpcallgraph -g -n -f png -o PHPCallGraph.png ../src/PHPCallGraph.php
../bin/phpcallgraph -g -f png -o phpcallgraph-library.png ../src/PHPCallGraph.php ../src/drivers/CallgraphDriver.php ../src/drivers/TextDriver.php ../src/drivers/GraphVizDriver.php ../lib/pear/Image/GraphViz.php
../bin/phpcallgraph -g -r -f png -o phpcallgraph-src.png ../src/
../bin/phpcallgraph -g -r -f png -o phpcallgraph.png ../src/ ../lib/
../bin/phpcallgraph -g -p -f png -o testfiles.png ../test/testfiles
