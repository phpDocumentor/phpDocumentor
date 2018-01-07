<?php

declare (strict_types=1);

namespace phpDocumentor\Application\CilexCompatibilityLayer;

use phpDocumentor\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CilexCompatibilityLayerBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        $this->container->get(Application::class);
    }

    public function registerCommands(\Symfony\Component\Console\Application $application)
    {
        if ($this->container->has('phpdocumentor.compatibility.extra_commands')) {
            $commands = $this->container->get('phpdocumentor.compatibility.extra_commands');

            foreach ($commands as $command) {
                $application->add($command);
            }
        }
    }
}
