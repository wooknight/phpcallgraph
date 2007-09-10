#!/bin/sh
../bin/phpcallgraph -n -f png -o PHPCallGraph.png ../src/PHPCallGraph.php
../bin/phpcallgraph -f png -o phpcallgraph-library.png ../src/drivers/ ../src/PHPCallGraph.php
../bin/phpcallgraph -r -f png -o phpcallgraph-src.png ../src/
../bin/phpcallgraph -r -f png -o phpcallgraph.png ../src/ ../lib/
../bin/phpcallgraph -p -f png -o testfiles.png ../test/testfiles
