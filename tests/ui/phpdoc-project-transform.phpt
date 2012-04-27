--TEST--
phpdoc project:transform
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:transform --config=none
--EXPECTF--
phpDocumentor version %s

ERROR: The given path "%s" either does not exist or is not readable.

This task will execute the transformation rules described in the given
template (defaults to 'responsive') with the given source (defaults to
output/structure.xml) and writes these to the target location (defaults to
'output').

It is possible for the user to receive additional information using the
verbose option or stop additional information using the quiet option. Please
take note that the quiet option also disables logging to file.

Usage:
 %s project:transform [options]

-h [--help]            Show this help message
-q [--quiet]           Silences the output and logging
-c [--config] [STRING] Configuration filename OR "none", when this option is omitted phpDocumentor tries to load the phpdoc.xml or phpdoc.dist.xml from the current working directory
-s [--source] [STRING] Path where the XML source file is located (optional, defaults to "output/structure.xml")
-t [--target] [STRING] Path where to store the generated output (optional, defaults to "output")
--template [STRING]    Name of the template to use (optional, defaults to "responsive")
-v [--verbose]         Outputs any information collected by this application, may slow down the process slightly
--parseprivate         Whether to parse DocBlocks marked with @internal tag
-p [--progressbar]     Whether to show a progress bar; will automatically quiet logging to stdout