--TEST--
phpdoc.php project:run -d phar://tests/data/test.phar -t build -c none
--FILE--
<?php
require_once 'tests/common/ui-include.php';
?>
--ARGS--
project:run -d phar://tests/data/test.phar -t build -c none
--EXPECTF--
phpDocumentor version %s

%s ERR (3): No DocBlock was found for function test2()
%s ERR (3): No page-level DocBlock was found in file phar://%s/test.phar/folder/test.php
%s ERR (3): No DocBlock was found for function test()
%s ERR (3): No page-level DocBlock was found in file phar://%s/test.phar/test.php
Starting transformation of files (this could take a while depending upon the size of your project)
Finished transformation in %s seconds
