--TEST--
phpdoc.php project:run -d phar://tests/data/test.phar -t build -c none
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -d phar://tests/data/test.phar -t build -c none
--EXPECTF--
Initializing parser and collecting files .. OK
Parsing files
  No DocBlock was found for function test2()
  No page-level DocBlock was found in file folder/test.php
  No DocBlock was found for function test()
  No page-level DocBlock was found in file test.php
Storing structure.xml in "%sbuild/structure.xml" .. OK
Initializing transformer .. OK
Processing behaviours .. OK
Executing transformations