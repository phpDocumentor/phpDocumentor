<?php
class Visibility
{
    public const PUBLIC_CONST = 'foo';
    protected const PROTECTED_CONST = 'foo';
    private const PRIVATE_CONST = 'foo';

    public $public_prop = 'foo';
    protected $protected_prop = 'foo';
    private $private_prop = 'foo';

    public function public_method() {}
    protected function protected_method() {}
    private function private_method() {}
}
