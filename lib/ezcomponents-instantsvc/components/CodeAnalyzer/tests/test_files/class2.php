<?php

echo 'do something malicious';

class FileDetailsTestClass2 {
	public function doSomething() {}
}

throw new Exception(__FILE__, 33);
return 'foo';

?>