--TEST--
docblox project:list
--FILE--
<?php
$_SERVER['argc']    = 2;
$_SERVER['argv'][1] = 'project:list';

// determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
if (strpos('@php_bin@', '@php_bin') === 0) {
    set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
}

if (!class_exists('Zend_Loader_Autoloader'))
{
  require_once 'Zend/Loader/Autoloader.php';
}
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DocBlox_');

$application = new DocBlox_Core_Application();
$application->main();
?>
--EXPECTF--
DocBlox version %s

theme
 :generate   Generates a skeleton theme.
 :list       Displays a listing of all available themes in DocBlox
project
 :list       Defines all tasks that can be run by DocBlox
 :parse      Parses the given source code and creates a structure file.
 :run        Parse and transform the given directory (-d|-f) to the given location (-t).
 :transform  Transforms the structure file into the specified output format
