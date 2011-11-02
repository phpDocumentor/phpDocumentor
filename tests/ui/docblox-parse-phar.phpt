--TEST--
docblox.php project:run -d phar://tests/data/test.phar -t build -c none
--FILE--
<?php
$_SERVER['argc']    = 8;
$_SERVER['argv'][1] = 'project:run';
$_SERVER['argv'][2] = '-d';
$_SERVER['argv'][3] = 'phar://'.dirname(__FILE__) . '/../data/test.phar';
$_SERVER['argv'][4] = '-t';
$_SERVER['argv'][5] = dirname(__FILE__) . '/../../build/';
$_SERVER['argv'][6] = '--config';
$_SERVER['argv'][7] = 'none';

require_once 'tests/common/ui-include.php';
?>
--EXPECTF--
DocBlox version %s

%s ERR (3): No DocBlock was found for File phar://%s/test.phar/folder/test.php
%s ERR (3): No DocBlock was found for Function test2
%s ERR (3): No DocBlock was found for File phar://%s/test.phar/test.php
%s ERR (3): No DocBlock was found for Function test
Starting transformation of files (this could take a while depending upon the size of your project)
Finished transformation in %s seconds
