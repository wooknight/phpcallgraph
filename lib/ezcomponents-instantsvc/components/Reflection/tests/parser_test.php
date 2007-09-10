<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionDocParserTest extends ezcTestCase
{
    /**
     * @var string[]
     */
    private static $docs;

    public function testGetTagsByName() {
        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[0]);
        $tags = $parser->getTagsByName('copyright');
        self::assertEquals(1, count($tags));

        $tags = $parser->getTagsByName('filesource');
        self::assertEquals(1, count($tags));

        $tags = $parser->getTagsByName('noneExistingTag');
        self::assertEquals(0, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[2]);
        $tags = $parser->getTagsByName('onetagonly');
        self::assertEquals(1, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[3]);
        $tags = $parser->getTagsByName('param');
        self::assertEquals(1, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[4]);
        $tags = $parser->getTagsByName('foobar');
        self::assertEquals(1, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[6]);
        $tags = $parser->getTagsByName('author');
        self::assertEquals(1, count($tags));
    }

    public function testGetTags() {
        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[0]);
        $tags = $parser->getTags();
        self::assertEquals(6, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[1]);
        $tags = $parser->getTags();
        self::assertEquals(0, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[2]);
        $tags = $parser->getTags();
        self::assertEquals(1, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[3]);
        $tags = $parser->getTags();
        self::assertEquals(2, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[4]);
        $tags = $parser->getTags();
        self::assertEquals(3, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[5]);
        $tags = $parser->getTags();
        self::assertEquals(0, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[6]);
        $tags = $parser->getTags();
        self::assertEquals(6, count($tags));
    }

    public function testGetParamTags() {
        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[0]);
        $tags = $parser->getParamTags();
        self::assertEquals(0, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[3]);
        $tags = $parser->getParamTags();
        self::assertEquals(1, count($tags));

        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[6]);
        $tags = $parser->getParamTags();
        self::assertEquals(3, count($tags));
        self::assertEquals('test', $tags[0]->getParamName());
        self::assertEquals('string', $tags[0]->getType());

        self::assertEquals('test3', $tags[2]->getParamName());
        self::assertEquals('NoneExistingType', $tags[2]->getType());
    }

    public function testGetVarTags() {
        $comment = <<<EOF
/**
* @var string
*/
EOF;
        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse($comment);
		$tags = $parser->getVarTags();
        self::assertEquals('string', $tags[0]->getType());
    }

    public function testGetReturnTags() {
        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[6]);
        $tags = $parser->getReturnTags();

        self::assertEquals('Hello World', $tags[0]->getDescription());
        self::assertEquals('string', $tags[0]->getType());
    }

    public function testIsTagged() {
        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse(self::$docs[6]);
        self::assertTrue($parser->isTagged('return'));
    }

    public function testGetShortDescription() {
        $class = new ReflectionClass('TestWebservice');
        $doc = $class->getDocComment();
        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse($doc);
        $desc = $parser->getShortDescription();

        self::assertEquals('This is the short description', $desc);
    }

    public function testGetLongDescription() {
        $class = new ReflectionClass('TestWebservice');
        $doc = $class->getDocComment();
        $parser = ezcReflectionApi::getDocParserInstance();
        $parser->parse($doc);
        $desc = $parser->getLongDescription();

        $expected = "This is the long description with may be additional infos and much more lines\nof text.\n\nEmpty lines are valide to.\n\nfoo bar";
        self::assertEquals($expected, $desc);
    }

    public static function suite()
    {
        self::$docs = array();
        $class = new ReflectionClass('ezcReflectionDocParserTest');
        self::$docs[] = $class->getDocComment();

        $class = new ReflectionClass('TestMethods');
        self::$docs[] = $class->getDocComment();
        $methods = $class->getMethods();

        foreach ($methods as $method) {
            self::$docs[] = $method->getDocComment();
        }

        return new PHPUnit_Framework_TestSuite( "ezcReflectionDocParserTest" );
    }
}
?>
