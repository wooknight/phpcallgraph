<?php
/**
 * File containing the ezcReflectionApi class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Holds type factory for generating type objects by given name
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionApi {

	/**
	 * @var ezcReflectionTypeFactory
	 */
	private static $reflectionTypeFactory = null;
	
	/**
	 * @var ezcReflectionDocParser
	 */
	private static $docParser = null;

	/**
	 * Don't allow objects, it is just a static factory
	 */
    private function __construct() {}

    public static function getDocParserInstance()
    {
    	if (self::$docParser == null) {
    		self::$docParser = new ezcReflectionPhpDocParser();
    	}
    	return clone self::$docParser;
    }
    
    public static function setDocParser($docParser)
    {
    	self::$docParser = $docParser;
    }
    
    /**
     * Factory to create type objects
     * @param ezcReflectionTypeFactory $factory
     * @return void
     */
    public static function setReflectionTypeFactory($factory) {
        self::$reflectionTypeFactory = $factory;
    }

    /**
     * Returns a ezcReflectionType object for the given type name
     *
     * @param string $typeName
     * @return ezcReflectionType
     */
    public static function getTypeByName($typeName) {
        if (self::$reflectionTypeFactory == null) {
            self::$reflectionTypeFactory = new ezcReflectionTypeFactoryImpl();
        }
        return self::$reflectionTypeFactory->getType($typeName);
    }
}

?>