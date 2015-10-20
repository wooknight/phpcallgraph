<?php
// try to find an SVN, Release or PEAR version of base.php
foreach (array('Base/src/base.php', 'Base/base.php', 'ezc/Base/base.php') as $ezcBaseFileToInclude) {
    if (!in_array('ezcBase', get_declared_classes())) {
        @include_once $ezcBaseFileToInclude;
    } else {
        break;
    }
}
// remove the global variable used in the foreach loop
unset($ezcBaseFileToInclude);

// add the InstantSVC components, e.g. the CodeAnalyzer, as an external class
// repository to the eZ Components autoloader
ezcBase::addClassRepository(
    realpath(dirname(__FILE__) . '/../lib/instantsvc/components'),
    realpath(dirname(__FILE__) . '/../lib/instantsvc/components/autoload'),
    'isc'
);

// define an __autoload function which is automatically called in case a class
// is used which hasn't been declared
function __autoload( $className ) {
    ezcBase::autoload( $className );
}
?>
