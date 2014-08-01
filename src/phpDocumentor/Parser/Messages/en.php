<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

return array(
    // File: phpDocumentor\Parser\Command\Project\ParseCommand (PPCPP)
    'PPCPP-DESCRIPTION' => 'Creates a structure file from your source code',
    'PPCPP-HELPTEXT' => <<<HELP
The parse task uses the source files defined either by -f or -d options and
generates cache files at the target location.
HELP
,
    // Parameter descriptions
    'PPCPP:OPT-FILENAME'           => 'Comma-separated list of files to parse. The wildcards ? and * are supported',
    'PPCPP:OPT-DIRECTORY'          => 'Comma-separated list of directories to (recursively) parse',
    'PPCPP:OPT-TARGET'             => 'Path where to store the cache (optional)',
    'PPCPP:OPT-ENCODING'           => 'Encoding to be used to interpret source files with',
    'PPCPP:OPT-EXTENSIONS'         => 'Comma-separated list of extensions to parse, defaults to php, php3 and phtml',
    'PPCPP:OPT-IGNORE'             => 'Comma-separated list of file(s) and directories that will be ignored. '
        . 'Wildcards * and ? are supported',
    'PPCPP:OPT-IGNORETAGS'         => 'Comma-separated list of tags that will be ignored, defaults to none. package, '
        . 'subpackage and ignore may not be ignored.',
    'PPCPP:OPT-HIDDEN'             => 'Use this option to tell phpDocumentor to parse files and directories that begin '
        . 'with a period (.), by default these are ignored',
    'PPCPP:OPT-IGNORESYMLINKS'     => 'Ignore symlinks to other files or directories, default is on',
    'PPCPP:OPT-MARKERS'            => 'Comma-separated list of markers/tags to filter',
    'PPCPP:OPT-TITLE'              => 'Sets the title for this project; default is the phpDocumentor logo',
    'PPCPP:OPT-FORCE'              => 'Forces a full build of the documentation, does not increment existing '
        . 'documentation',
    'PPCPP:OPT-VALIDATE'           => 'Validates every processed file using PHP Lint, costs a lot of performance',
    'PPCPP:OPT-VISIBILITY'         => 'Specifies the parse visibility that should be displayed in the documentation '
        . '(comma separated e.g. "public,protected")',
    'PPCPP:OPT-DEFAULTPACKAGENAME' => 'Name to use for the default package.',
    'PPCPP:OPT-SOURCECODE'         => 'Whether to include syntax highlighted source code',
    'PPCPP:OPT-PROGRESSBAR'        => 'Whether to show a progress bar; will automatically quiet logging to stdout',
    'PPCPP:OPT-PARSEPRIVATE'       => 'Whether to parse DocBlocks marked with @internal tag',

    // Log and exception messages
    'PPCPP:LOG-COLLECTING'   => 'Collecting files .. ',
    'PPCPP:LOG-OK'           => 'OK',
    'PPCPP:LOG-INITIALIZING' => 'Initializing parser .. ',
    'PPCPP:LOG-PARSING'      => 'Parsing files',
    'PPCPP:LOG-STORECACHE'   => 'Storing cache in "%s" .. ',
    'PPCPP:EXC-NOFILES'      => 'No parsable files were found, did you specify any using the -f or -d parameter?',
    'PPCPP:EXC-BADTARGET'    => 'The provided target location must be a directory',
    'PPCPP:EXC-NOPARTIAL'    => 'Partial "%s" not readable or found.',
);
