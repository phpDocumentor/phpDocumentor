<?php

declare(strict_types=1);

namespace MyExtension\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class MyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('rot13', function ($string) {
                return str_rot13($string);
            })
        ];
    }

}
