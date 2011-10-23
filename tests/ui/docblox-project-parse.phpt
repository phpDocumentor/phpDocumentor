--TEST--
docblox project:parse
--FILE--
<?php
$_SERVER['argc']    = 2;
$_SERVER['argv'][1] = 'project:parse';
$_SERVER['argv'][2] = '--config';
$_SERVER['argv'][3] = 'none';

require_once 'tests/common/ui-include.php';

?>
--EXPECTF--
DocBlox version %s

ERROR: %s

The parse task uses the source files defined either by -f or -d options and generates a structure
file (structure.xml) at the target location (which is the folder 'output' unless the option -t is provided).

Usage:
 %s project:parse [options]

-h [--help]                   Show this help message
-q [--quiet]                  Silences the output and logging
-c [--config] [STRING]        Configuration filename OR "none", when this option is omitted DocBlox tries to load the docblox.xml or docblox.dist.xml from the current working directory
-f [--filename] STRING        Comma-separated list of files to parse. The wildcards ? and * are supported
-d [--directory] STRING       Comma-separated list of directories to (recursively) parse.
-t [--target] [STRING]        Path where to store the generated output (optional, defaults to "")
-e [--extensions] [STRING]    Optional comma-separated list of extensions to parse, defaults to php, php3 and phtml
-i [--ignore] [STRING]        Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported
-m [--markers] [STRING]       Comma-separated list of markers/tags to filter, (optional, defaults to: "TODO,FIXME")
-v [--verbose]                Provides additional information during parsing, usually only needed for debugging purposes
--title [STRING]              Sets the title for this project; default is the DocBlox logo
--force                       Forces a full build of the documentation, does not increment existing documentation
--validate                    Validates every processed file using PHP Lint, costs a lot of performance
--visibility [STRING]         Specifies the parse visibility that should be displayed in the documentation (comma seperated e.g. "public,protected")
--defaultpackagename [STRING] name to use for the default package.  If not specified, uses "default"
--sourcecode                  Whether to include syntax highlighted source code
-p [--progressbar]            Whether to show a progress bar; will automatically quiet logging to stdout
