#! /bin/sh


DRIVER=umlgraph


FORMATS="svg png"
TEST1=testclasses
testclassesLIBS="../test/testfiles/TestClass.php ../test/testfiles/*.php"

TEST2=umlgraph
umlgraphLIBS="../src/PHPCallGraph.php ../src/drivers/UmlGraphSequenceDiagramDriver.php"

TESTS="$TEST1 $TEST2"

for TEST in $TESTS; do
    LIBSVAR=${TEST}LIBS
#    LIBS=${${LIBSVAR}}
    LIBS=$umlgraphLIBS
    UMLSEQFILE=$TEST.$DRIVER
    ../bin/phpcallgraph -g -p -f $DRIVER -o $UMLSEQFILE $LIBS
    for FORMAT in $FORMATS; do 
	OUTPUTFILE=$UMLSEQFILE.$FORMAT
	pic2plot $UMLSEQFILE -T$FORMAT > $OUTPUTFILE
    done
    ../bin/phpcallgraph -g -f png -o $TEST.png $LIBSUMLG
done



