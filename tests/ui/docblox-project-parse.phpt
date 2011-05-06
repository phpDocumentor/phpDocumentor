--TEST--
docblox project:parse
--FILE--
<?php
$_SERVER['argc']    = 2;
$_SERVER['argv'][1] = 'project:parse';
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

ERROR: %s

The parse task uses the source files defined either by -f or -d options and generates a structure
file (structure.xml) at the target location (which is the folder 'output' unless the option -t is provided).

Usage:
 %s project:parse [options]

-h [--help]                Show this help message
-q [--quiet]               Silences the output and logging
-f [--filename] STRING     Comma-separated list of files to parse. The wildcards ? and * are supported
-d [--directory] STRING    Comma-separated list of directories to (recursively) parse.
-t [--target] [STRING]     Path where to store the generated output (optional, defaults to "")
-e [--extensions] [STRING] Optional comma-separated list of extensions to parse, defaults to php, php3 and phtml
-i [--ignore] [STRING]     Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported
-m [--markers] [STRING]    Comma-separated list of markers/tags to filter, (optional, defaults to: "TODO,FIXME")
-v [--verbose]             Provides additional information during parsing, usually only needed for debugging purposes
--title [STRING]           Sets the title for this project; default is the DocBlox logo
--force                    Forces a full build of the documentation, does not increment existing documentation
--validate                 Validates every processed file using PHP Lint, costs a lot of performance
-c [--config] [STRING]     Configuration filename OR "none", when this option is omitted DocBlox tries to load the docblox.xml or docblox.dist.xml from the current working directory
