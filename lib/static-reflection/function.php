<?php
/**
 * 
 * 
 * @author Falko Menge <fakko at users dot sourceforge dot net>
 * @copyright 2009 Falko Menge
 * @package StaticReflection
 */

/**
 * 
 * 
 * @package 
 * @subpackage
 * @property string  $fileName
 * @property integer $startLine
 */
class staticReflectionFunction
    extends ReflectionFunctionAbstract                                                                                 
    implements Reflector                                                                                               
{

    /**
     * 
     */
    const IS_DEPRECATED = 262144;

    /**
     * @var string Name of the function.
     */
    public $name;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Constructor.
     * 
     * Will be called on each newly-created object.
     */
    public function __construct( $name, $fileName )
    {
        //$this->name = $name; //ReflectionException: Cannot set read-only property staticReflectionFunction::$name
        $this->properties['name'] = $name;
        $this->fileName = $fileName;
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws Exception if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'fileName':
            case 'startLine':
                $this->properties[$name] = $value;
                break;
            default:
                throw new Exception( "No such property name '{$name}'." );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws Exception if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'fileName':
            case 'startLine':
                return $this->properties[$name];
        }
        throw new Exception( "No such property name '{$name}'." );
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'fileName':
            case 'startLine':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
        // if there is no default case before:
        return parent::__isset( $name );
    }

    /**
     * Returns the string representation of the current object.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * 
     * 
     * @param mixed name 
     * @param mixed return 
     * @return mixed
     */
    public static function export( $name, $return = false )
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function isDisabled()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @param mixed args 
     * @return mixed
     */
    public function invoke( $args )
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @param array args 
     * @return mixed
     */
    public function invokeArgs( array $args )
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * Creates a copy of the object.
     * 
     * An object copy is created by using the clone keyword (which calls the
     * object's __clone() method if possible). The method cannot be called
     * directly.
     * @return ReflectionFunctionAbstract
     */
    /*
    final private function __clone()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }
    */

    /**
     * 
     * 
     * @return mixed
     */
    public function isInternal()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function isUserDefined()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return string
     */
    public function getName()
    {
        return $this->properties['name'];
    }

    /**
     * 
     * 
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * 
     * 
     * @return integer
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getEndLine()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getDocComment()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getStaticVariables()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function returnsReference()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getParameters()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getNumberOfParameters()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getNumberOfRequiredParameters()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getExtension()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function getExtensionName()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }

    /**
     * 
     * 
     * @return mixed
     */
    public function isDeprecated()
    {
        //TODO: Add implementation
        throw new Exception(
            'Method __CLASS__::__METHOD__ is not implemented.'
        );
    }
}

