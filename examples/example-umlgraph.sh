#! /bin/sh


DRIVER=umlgraph


FORMATS="svg png"
TEST1=testclasses
testclassesLIBS="../test/testfiles/TestClass.php ../test/testfiles/*.php"

TEST2=umlgraph
umlgraphLIBS="../src/PHPCallGraph.php ../src/drivers/UmlGraphSequenceDiagramDriver.php"

TESTS="$TEST1 $TEST2"
DEBUG=-g

for TEST in $TESTS; do
    LIBSVAR=${TEST}LIBS
#    LIBS=${${LIBSVAR}}
    LIBS=$umlgraphLIBS
    UMLSEQFILE=$TEST.$DRIVER
    ../bin/phpcallgraph $DEBUG -f $DRIVER -o $UMLSEQFILE $LIBS
done



