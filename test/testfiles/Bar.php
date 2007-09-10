<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once 'Foo.php';

class Bar {

    /**
     * @webmethod
     * @return string
     */
    public function sayHello() {
        return 'Hello World!';
    }

    /**
     * @webmethod
     * @param int $x
     * @param int $y
     * @return int
     */
    public function add($x, $y) {
        return $x+$y;
    }

    /**
     * @webmethod
     * @param float[] $array
     * @return float
     */
    public function arraySum($array) {
      $sum = 0;
      if (is_array($array)) {
        $sum = array_sum($array);
      }
      return $sum;
    }   
  
    /**
     * @webmethod
     * @return Foo
     */
    public function getFoo() {
        $returnValue = new Foo();
	return $returnValue;
    }
    
    /**
     * @webmethod
     * @param Foo $inputFoo
     * @return Foo[]
     */
    public function duplicateFoo($inputFoo) {
        $returnValue[] = $inputFoo;
        $returnValue[] = $inputFoo;
	return $returnValue;
    }
}
?>
