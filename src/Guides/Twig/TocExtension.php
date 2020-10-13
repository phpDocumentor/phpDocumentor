<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\Twig;

use phpDocumentor\Guides\Metas;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TocExtension extends AbstractExtension
{
    /** @var Metas */
    private $metas;

    public function __construct(Metas $metas)
    {
        $this->metas = $metas;
    }

    public function getFunctions() : array
    {
        return [
            new TwigFunction('menu', [$this, 'menu']),
        ];
    }

    public function menu() : array
    {
        $index = $this->metas->get('index');

        if ($index === null) {
            return [];
        }

        $menu = [
            'label' => $index->getTitle(),
            'path' => $index->getUrl(),
            'items' => []
        ];

        foreach ($index->getTocs()[0] ?? [] as $url) {
            $meta = $this->metas->get($url);
            $menu['items'][] = [
                'label' => $meta->getTitle(),
                'path' => $meta->getUrl(),
                'items' => []
            ];
        }

        return $menu;
    }
}
