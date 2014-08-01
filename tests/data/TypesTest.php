<?php
include('NamespaceTestData.php');
include('../../src/phpDocumentor/Token.php');

class TypesTest
{

    /**
    * @param string $a
    * @param null   $b
    * @param array  $c
    * @param bool   $d
    * @param bool   $e
    *
    * @return void
    */
    public function test($a = 'null', $b = null, $c = array(), $d = true, $e = false)
    {
    }
}
