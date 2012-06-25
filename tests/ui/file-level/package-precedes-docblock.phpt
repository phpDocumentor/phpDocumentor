--TEST--
phpdoc project:parse -f tests/data/file-level/PackagePrecedesDocBlock.php -t build
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:parse -f tests/data/file-level/PackagePrecedesDocBlock.php -t build --config=none
--EXPECTF--
Initializing parser and collecting files .. OK
Parsing files
Storing structure.xml in "%sbuild/structure.xml" .. OK
