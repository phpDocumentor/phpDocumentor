--TEST--
docblox project:run
--FILE--
<?php
$_SERVER['argc']    = 3;
$_SERVER['argv'][1] = 'project:run';
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

ERROR: No parsable files were found, did you specify any using the -f or -d parameter?

DocBlox creates documentation from PHP source files. The simplest way to use it is:

    $ docblox run -d <directory to parse> -t <output directory>

This will parse every file ending with .php, .php3 and .phtml in <directory to parse> and then
output a HTML site containing easily readable documentation in <output directory>.

DocBlox will try to look for a docblox.dist.xml or docblox.xml file in your current working directory
and use that to override the default settings if present. In the configuration file can you specify the
same settings (and more) as the command line provides.

Usage:
 %s project:run [options]

-h [--help]                Show this help message
-q [--quiet]               Silences the output and logging
-f [--filename] STRING     Comma-separated list of files to parse. The wildcards ? and * are supported
-d [--directory] STRING    Comma-separated list of directories to (recursively) parse.
-t [--target] [STRING]     Path where to store the generated output (optional, defaults to "output")
-e [--extensions] [STRING] Optional comma-separated list of extensions to parse, defaults to php, php3 and phtml
-i [--ignore] [STRING]     Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported
-m [--markers] [STRING]    Comma-separated list of markers/tags to filter, (optional, defaults to: "TODO,FIXME")
-v [--verbose]             Provides additional information during parsing, usually only needed for debugging purposes
--title [STRING]           Sets the title for this project; default is the DocBlox logo
--template [STRING]        Sets the template to use when generating the output
--force                    Forces a full build of the documentation, does not increment existing documentation
--validate                 Validates every processed file using PHP Lint, costs a lot of performance
-c [--config] [STRING]     Configuration filename OR "none", when this option is omitted DocBlox tries to load the docblox.xml or docblox.dist.xml from the current working directory
