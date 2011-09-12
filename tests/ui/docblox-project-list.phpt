--TEST--
docblox project:list
--FILE--
<?php
$_SERVER['argc']    = 2;
$_SERVER['argv'][1] = 'project:list';

require_once 'tests/common/ui-include.php';

?>
--EXPECTF--
DocBlox version %s

project
 :list       Defines all tasks that can be run by DocBlox
 :parse      Parses the given source code and creates a structure file.
 :run        Parse and transform the given directory (-d|-f) to the given location (-t).
 :transform  Transforms the structure file into the specified output format
theme
 :generate   Generates a skeleton theme.
 :list       Displays a listing of all available themes in DocBlox
