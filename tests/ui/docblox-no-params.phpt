--TEST--
docblox project:run
--FILE--
<?php
$_SERVER['argc']    = 3;
$_SERVER['argv'][1] = 'project:run';
$_SERVER['argv'][2] = '--config';
$_SERVER['argv'][3] = 'none';

require_once 'tests/common/ui-include.php';
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

-h [--help]                   Show this help message
-q [--quiet]                  Silences the output and logging
-c [--config] [STRING]        Configuration filename OR "none", when this option is omitted DocBlox tries to load the docblox.xml or docblox.dist.xml from the current working directory
-f [--filename] STRING        Comma-separated list of files to parse. The wildcards ? and * are supported
-d [--directory] STRING       Comma-separated list of directories to (recursively) parse.
-t [--target] [STRING]        Path where to store the generated output (optional, defaults to "output")
-e [--extensions] [STRING]    Optional comma-separated list of extensions to parse, defaults to php, php3 and phtml
-i [--ignore] [STRING]        Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported
-m [--markers] [STRING]       Comma-separated list of markers/tags to filter, (optional, defaults to: "TODO,FIXME")
-v [--verbose]                Provides additional information during parsing, usually only needed for debugging purposes
--title [STRING]              Sets the title for this project; default is the DocBlox logo
--template [STRING]           Sets the template to use when generating the output
--force                       Forces a full build of the documentation, does not increment existing documentation
--validate                    Validates every processed file using PHP Lint, costs a lot of performance
--parseprivate                Whether to parse DocBlocks tagged with @internal
--visibility [STRING]         Specifies the parse visibility that should be displayed in the documentation (comma seperated e.g. "public,protected")
--defaultpackagename [STRING] name to use for the default package.  If not specified, uses "default"
--sourcecode                  Whether to include syntax highlighted source code
-p [--progressbar]            Whether to show a progress bar; will automatically quiet logging to stdout