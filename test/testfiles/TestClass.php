<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once 'Foo.php';
require_once 'Bar.php';

class TestClass {

    function __construct() {
        $myFoo = new Foo();
        $this->test(1, array(), $this, new stdClass(), null);
        $myFoo->getInputString();
        $myFoo->inputString = 'bar';
        Bar::add(1, 1);
        self::test(1, array(), $this, new stdClass(), null); 
        Test::test(1, array(), $this, new stdClass(), null);
        userDefinedFunction(1, array(), $this, new stdClass(), null); 
        time();
    }

    function test($nix, Array $ar, &$ref, $std, $na, $opt = NULL, $def = "FooBar") {
    	$this->ambiguous();
    }

    function ambiguous() {
    	$myBar = new Bar();
	    $myBar->ambiguous();
    }

}

function userDefinedFunction($nix, Array $ar, &$ref, $std, $na, $opt = NULL, $def = "FooBar") {
}
?>
