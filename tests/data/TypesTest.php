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
     */
    public function testTest($a = 'null', $b = null, $c = [], $d = true, $e = false)
    {
    }
}
