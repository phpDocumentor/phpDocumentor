--TEST--
docblox project:transform
--FILE--
<?php
$_SERVER['argc']    = 2;
$_SERVER['argv'][1] = 'project:transform';
$_SERVER['argv'][2] = '--config';
$_SERVER['argv'][3] = 'none';

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

ERROR: The given location "%s" is not a folder.

This task will execute the transformation rules described in the given template (defaults to 'default') with the
given source (defaults to output/structure.xml) and writes these to the target location (defaults to 'output').

It is possible for the user to receive additional information using the verbose option or stop additional
information using the quiet option. Please take note that the quiet option also disables logging to file.

Usage:
 %s project:transform [options]

-h [--help]            Show this help message
-q [--quiet]           Silences the output and logging
-s [--source] [STRING] Path where the structure.xml is located (optional, defaults to "output/structure.xml")
-t [--target] [STRING] Path where to store the generated output (optional, defaults to "output")
--template [STRING]    Name of the template to use (optional, defaults to "default")
-v [--verbose]         Outputs any information collected by this application, may slow down the process slightly
-c [--config] [STRING] Configuration filename OR "none", when this option is omitted DocBlox tries to load the docblox.xml or docblox.dist.xml from the current working directory
